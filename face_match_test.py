import cv2
import os
import time

MODEL_PATH = "storage/app/face-models/"
REFERENCE_PATH = (
    "storage/app/public/student-photos/"
    "87cdfed9-8f39-4168-ac8f-cea83e975794.jpg"
)

# Load the recognition model
recognizer = cv2.FaceRecognizerSF_create(
    MODEL_PATH + "face_recognition_sface_2021dec.onnx",
    ""
)

# Load the face detector
detector = cv2.FaceDetectorYN_create(
    MODEL_PATH + "face_detection_yunet_2023mar.onnx",
    "",
    (640, 480),
    0.8,
    0.3,
    5000
)

# Load the registered student's reference photo
reference = cv2.imread(REFERENCE_PATH)

if reference is None:
    raise SystemExit("ERROR: Cannot load student reference photo.")

height, width = reference.shape[:2]
detector.setInputSize((width, height))

_, reference_faces = detector.detect(reference)

if reference_faces is None or len(reference_faces) != 1:
    raise SystemExit(
        "ERROR: Reference photo must contain exactly one detectable face."
    )

reference_aligned = recognizer.alignCrop(
    reference,
    reference_faces[0]
)

reference_feature = recognizer.feature(reference_aligned)

print("Reference face loaded successfully.")
print("Opening webcam...")

camera = cv2.VideoCapture(0, cv2.CAP_DSHOW)

if not camera.isOpened():
    raise SystemExit("ERROR: Cannot access webcam.")

print("Look at the camera. Press Q to quit.")

last_score = None
last_capture = 0

try:
    while True:
        success, frame = camera.read()

        if not success:
            break

        height, width = frame.shape[:2]
        detector.setInputSize((width, height))

        _, faces = detector.detect(frame)

        if faces is not None and len(faces) == 1:
            face = faces[0]

            x, y, w, h = face[:4].astype(int)

            aligned = recognizer.alignCrop(frame, face)
            live_feature = recognizer.feature(aligned)

            score = recognizer.match(
                reference_feature,
                live_feature,
                cv2.FaceRecognizerSF_FR_COSINE
            )

            last_score = score
            last_capture = time.time()

            cv2.rectangle(
                frame,
                (x, y),
                (x + w, y + h),
                (0, 255, 0),
                2
            )

            cv2.putText(
                frame,
                f"Similarity: {score:.4f}",
                (10, 30),
                cv2.FONT_HERSHEY_SIMPLEX,
                0.7,
                (0, 255, 0),
                2
            )

        elif faces is not None and len(faces) > 1:
            cv2.putText(
                frame,
                "Only one face allowed",
                (10, 30),
                cv2.FONT_HERSHEY_SIMPLEX,
                0.7,
                (0, 0, 255),
                2
            )
        else:
            cv2.putText(
                frame,
                "No face detected",
                (10, 30),
                cv2.FONT_HERSHEY_SIMPLEX,
                0.7,
                (0, 0, 255),
                2
            )

        cv2.imshow("ARGUS-LAN Face Matching Test", frame)

        if cv2.waitKey(1) & 0xFF == ord("q"):
            break

finally:
    camera.release()
    cv2.destroyAllWindows()

if last_score is not None:
    print(f"\nLast similarity score: {last_score:.4f}")
    print("This is a development measurement, not an authentication decision.")
else:
    print("No valid face comparison was captured.")
