import cv2
import random
import time
import statistics

MODEL_PATH = "storage/app/face-models/"

REFERENCE_PATH = (
    "storage/app/public/student-photos/"
    "87cdfed9-8f39-4168-ac8f-cea83e975794.jpg"
)

# Experimental threshold only
SIMILARITY_THRESHOLD = 0.50

TURN_THRESHOLD = 0.18
CENTER_THRESHOLD = 0.10

# Load SFace recognition model
recognizer = cv2.FaceRecognizerSF_create(
    MODEL_PATH + "face_recognition_sface_2021dec.onnx",
    ""
)

# Load YuNet detector
detector = cv2.FaceDetectorYN_create(
    MODEL_PATH + "face_detection_yunet_2023mar.onnx",
    "",
    (640, 480),
    0.8,
    0.3,
    5000
)


def estimate_yaw(face):
    right_eye = face[4:6]
    left_eye = face[6:8]
    nose = face[8:10]

    eye_center_x = (right_eye[0] + left_eye[0]) / 2
    eye_distance = abs(right_eye[0] - left_eye[0])

    if eye_distance < 1:
        return None

    return (nose[0] - eye_center_x) / eye_distance


# Load registered student's reference photo
reference = cv2.imread(REFERENCE_PATH)

if reference is None:
    raise SystemExit("ERROR: Cannot load reference photo.")

height, width = reference.shape[:2]
detector.setInputSize((width, height))

_, reference_faces = detector.detect(reference)

if reference_faces is None or len(reference_faces) != 1:
    raise SystemExit(
        "ERROR: Reference photo must contain exactly one face."
    )

reference_aligned = recognizer.alignCrop(
    reference,
    reference_faces[0]
)

reference_feature = recognizer.feature(reference_aligned)

print("Registered student reference loaded.")

# Open webcam
camera = cv2.VideoCapture(0, cv2.CAP_DSHOW)

if not camera.isOpened():
    raise SystemExit("ERROR: Cannot access webcam.")

# Random challenge
challenge = random.choice(["TURN LEFT", "TURN RIGHT"])

print("\nARGUS-LAN COMBINED AUTHENTICATION TEST")
print("Follow the camera instructions.")

state = "CALIBRATE"
baseline_values = []

turn_frames = 0
center_frames = 0

baseline_yaw = None
challenge_start = None
final_score = None
authentication_passed = False

print("\nLook straight at the camera.")

try:
    while True:
        success, frame = camera.read()

        if not success:
            print("ERROR: Camera frame unavailable.")
            break

        height, width = frame.shape[:2]
        detector.setInputSize((width, height))

        _, faces = detector.detect(frame)

        message = "POSITION YOUR FACE IN CAMERA"
        color = (0, 0, 255)

        if faces is not None and len(faces) == 1:

            face = faces[0]

            x, y, w, h = face[:4].astype(int)

            yaw = estimate_yaw(face)

            cv2.rectangle(
                frame,
                (x, y),
                (x + w, y + h),
                (0, 255, 0),
                2
            )

            if yaw is not None:

                # STEP 1: Establish neutral head position
                if state == "CALIBRATE":

                    baseline_values.append(yaw)

                    message = (
                        f"Keep looking straight: "
                        f"{len(baseline_values)}/20"
                    )
                    color = (0, 255, 255)

                    if len(baseline_values) >= 20:

                        baseline_yaw = statistics.median(
                            baseline_values
                        )

                        challenge_start = time.time()
                        state = "TURN"

                        print("\nChallenge:", challenge)

                # STEP 2: Perform randomized head turn
                elif state == "TURN":

                    difference = yaw - baseline_yaw

                    if challenge == "TURN LEFT":
                        detected = difference < -TURN_THRESHOLD
                    else:
                        detected = difference > TURN_THRESHOLD

                    if detected:
                        turn_frames += 1
                    else:
                        turn_frames = 0

                    message = challenge
                    color = (0, 255, 255)

                    if turn_frames >= 3:
                        state = "RETURN"
                        print("Head movement detected.")
                        print("Return your head to the center.")

                # STEP 3: Return to neutral position
                elif state == "RETURN":

                    difference = yaw - baseline_yaw

                    if abs(difference) <= CENTER_THRESHOLD:
                        center_frames += 1
                    else:
                        center_frames = 0

                    message = "RETURN TO CENTER"
                    color = (0, 255, 255)

                    if center_frames >= 5:

                        print("Center position confirmed.")
                        print("Checking registered identity...")

                        # Capture a fresh centered frame

                        # Capture 10 fresh face frames for verification
                        print("Collecting 10 face comparisons...")

                        scores = []
                        deadline = time.time() + 8

                        while len(scores) < 10 and time.time() < deadline:

                            frame_ok, verify_frame = camera.read()

                            if not frame_ok:
                                continue

                            verify_height, verify_width = verify_frame.shape[:2]
                            detector.setInputSize((verify_width, verify_height))

                            _, verify_faces = detector.detect(verify_frame)

                            # Only accept frames containing exactly one face
                            if verify_faces is None or len(verify_faces) != 1:
                                continue

                            verify_face = verify_faces[0]

                            verify_aligned = recognizer.alignCrop(
                                verify_frame,
                                verify_face
                            )

                            verify_feature = recognizer.feature(verify_aligned)

                            score = recognizer.match(
                                reference_feature,
                                verify_feature,
                                cv2.FaceRecognizerSF_FR_COSINE
                            )

                            scores.append(float(score))

                            print(
                                f"Frame {len(scores)}/10: {float(score):.4f}"
                            )

                        # Evaluate all 10 frames
                        if len(scores) == 10:

                            final_score = statistics.median(scores)

                            matching_frames = sum(
                                score >= SIMILARITY_THRESHOLD
                                for score in scores
                            )

                            print(f"Median similarity: {final_score:.4f}")
                            print(f"Matching frames: {matching_frames}/10")

                            if (
                                final_score >= SIMILARITY_THRESHOLD
                                and matching_frames >= 7
                            ):
                                authentication_passed = True
                                state = "PASSED"
                                print("\nAUTHENTICATION TEST PASSED.")
                            else:
                                state = "FAILED"
                                print("\nFACE MATCH FAILED.")

                        else:
                            state = "FAILED"
                            print("\nFACE MATCH FAILED: Could not collect 10 valid frames.")


                elif state == "PASSED":
                    message = "AUTHENTICATION TEST PASSED"
                    color = (0, 255, 0)

                elif state == "FAILED":
                    message = "FACE MATCH FAILED"
                    color = (0, 0, 255)

                if final_score is not None:
                    cv2.putText(
                        frame,
                        f"Similarity: {final_score:.4f}",
                        (10, 70),
                        cv2.FONT_HERSHEY_SIMPLEX,
                        0.7,
                        color,
                        2
                    )

        elif faces is not None and len(faces) > 1:
            message = "ONLY ONE FACE ALLOWED"
            color = (0, 0, 255)

            turn_frames = 0
            center_frames = 0

        cv2.putText(
            frame,
            message,
            (10, 35),
            cv2.FONT_HERSHEY_SIMPLEX,
            0.65,
            color,
            2
        )

        cv2.imshow("ARGUS-LAN Authentication Test", frame)

        if state in ("PASSED", "FAILED"):
            cv2.waitKey(2500)
            break

        if (
            challenge_start is not None
            and time.time() - challenge_start > 15
        ):
            print("\nLIVENESS CHALLENGE FAILED: Timeout.")
            break

        if cv2.waitKey(1) & 0xFF == ord("q"):
            break

finally:
    camera.release()
    cv2.destroyAllWindows()

print("\n========== TEST RESULT ==========")

if final_score is not None:
    print(f"Similarity score: {final_score:.4f}")

if authentication_passed:
    print("Liveness: PASSED")
    print("Face recognition: PASSED")
    print("Combined test: PASSED")
else:
    print("Combined test: FAILED")

print("This is a local development test, not production authentication.")
