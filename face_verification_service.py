import os
import secrets
import statistics
from pathlib import Path
from typing import List

import cv2
import numpy as np
from fastapi import (
    Depends,
    FastAPI,
    File,
    Header,
    HTTPException,
    UploadFile
)
from pydantic import BaseModel

BASE_DIR = Path(__file__).resolve().parent
MODEL_DIR = BASE_DIR / "storage" / "app" / "face-models"

SIMILARITY_THRESHOLD = 0.50
MIN_MATCHING_FRAMES = 7
REQUIRED_FRAMES = 10
MAX_IMAGE_SIZE = 5 * 1024 * 1024

app = FastAPI(
    title="ARGUS-LAN Face Verification Service",
    version="1.0.0"
)
# Internal secret shared only with the Laravel server.
FACE_SERVICE_TOKEN = os.environ.get("ARGUS_FACE_SERVICE_TOKEN")


def verify_service_token(
    authorization: str = Header(default=None)
):
    if not FACE_SERVICE_TOKEN:
        raise HTTPException(
            status_code=503,
            detail="Face service authentication is not configured."
        )

    expected = f"Bearer {FACE_SERVICE_TOKEN}"

    if not authorization or not secrets.compare_digest(
        authorization,
        expected
    ):
        raise HTTPException(
            status_code=401,
            detail="Unauthorized face service request."
        )

# Load the recognition models once at startup.
recognizer = cv2.FaceRecognizerSF_create(
    str(MODEL_DIR / "face_recognition_sface_2021dec.onnx"),
    ""
)

detector = cv2.FaceDetectorYN_create(
    str(MODEL_DIR / "face_detection_yunet_2023mar.onnx"),
    "",
    (640, 480),
    0.8,
    0.3,
    5000
)


class VerificationResult(BaseModel):
    verified: bool
    median_similarity: float
    matching_frames: int
    total_frames: int
    message: str


def decode_image(image_bytes: bytes):
    image_array = np.frombuffer(image_bytes, dtype=np.uint8)
    image = cv2.imdecode(image_array, cv2.IMREAD_COLOR)

    if image is None:
        raise ValueError("Invalid image file.")

    return image


def extract_face_feature(image, image_label):
    height, width = image.shape[:2]
    detector.setInputSize((width, height))

    _, faces = detector.detect(image)

    face_count = 0 if faces is None else len(faces)

    if face_count != 1:
        raise ValueError(
            f"{image_label}: expected exactly one detectable face, "
            f"but detected {face_count}."
        )

    aligned_face = recognizer.alignCrop(image, faces[0])
    feature = recognizer.feature(aligned_face)

    return feature


@app.get("/health")
def health():
    return {
        "status": "running",
        "service": "ARGUS-LAN Face Verification"
    }


@app.post(
    "/verify",
    response_model=VerificationResult
)
async def verify_face(
    reference: UploadFile = File(...),
    frames: List[UploadFile] = File(...),
    _auth: None = Depends(verify_service_token)
):
    if len(frames) != REQUIRED_FRAMES:
        raise HTTPException(
            status_code=422,
            detail=f"Exactly {REQUIRED_FRAMES} frames are required."
        )

    try:
        reference_bytes = await reference.read(
            MAX_IMAGE_SIZE + 1
        )

        if len(reference_bytes) > MAX_IMAGE_SIZE:
            raise HTTPException(
                status_code=413,
                detail="Reference image is too large."
            )

        reference_image = decode_image(reference_bytes)
        reference_feature = extract_face_feature(reference_image, 'Reference photo')

        scores = []

        for index, uploaded_frame in enumerate(frames, start=1):

            frame_bytes = await uploaded_frame.read(
                MAX_IMAGE_SIZE + 1
            )

            if len(frame_bytes) > MAX_IMAGE_SIZE:
                raise HTTPException(
                    status_code=413,
                    detail=f"Frame {index} is too large."
                )

            frame_image = decode_image(frame_bytes)
            frame_feature = extract_face_feature(frame_image, f'Webcam frame {index}')

            score = recognizer.match(
                reference_feature,
                frame_feature,
                cv2.FaceRecognizerSF_FR_COSINE
            )

            scores.append(float(score))

        median_score = statistics.median(scores)

        matching_frames = sum(
            score >= SIMILARITY_THRESHOLD
            for score in scores
        )

        verified = (
            median_score >= SIMILARITY_THRESHOLD
            and matching_frames >= MIN_MATCHING_FRAMES
        )

        return VerificationResult(
            verified=verified,
            median_similarity=round(median_score, 4),
            matching_frames=matching_frames,
            total_frames=len(scores),
            message=(
                "Face verification passed."
                if verified
                else "Face verification failed."
            )
        )

    except ValueError as error:
        raise HTTPException(
            status_code=422,
            detail=str(error)
        )

    finally:
        await reference.close()

        for uploaded_frame in frames:
            await uploaded_frame.close()
