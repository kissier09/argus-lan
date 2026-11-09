import cv2
import random
import time
import math

MODEL_PATH = "storage/app/face-models/"
CAMERA_INDEX = 0

# Load YuNet face detector
detector = cv2.FaceDetectorYN_create(
    MODEL_PATH + "face_detection_yunet_2023mar.onnx",
    "",
    (640, 480),
    0.8,
    0.3,
    5000
)

camera = cv2.VideoCapture(CAMERA_INDEX, cv2.CAP_DSHOW)

if not camera.isOpened():
    raise SystemExit("ERROR: Cannot access webcam.")

challenge = random.choice(["TURN LEFT", "TURN RIGHT"])
start_time = time.time()
challenge_timeout = 12

completed = False
baseline_yaw = None

print("ARGUS-LAN Liveness Test")
print("Challenge:", challenge)
print("Follow the instruction shown on camera.")
print("Press Q to quit.")

def estimate_yaw(face):
    # YuNet landmark positions:
    # 0 = right eye, 1 = left eye
    # 2 = nose tip, 3 = right mouth corner
    # 4 = left mouth corner

    right_eye = face[4:6]
    left_eye = face[6:8]
    nose = face[8:10]

    eye_center_x = (right_eye[0] + left_eye[0]) / 2
    eye_distance = abs(right_eye[0] - left_eye[0])

    if eye_distance < 1:
        return None

    return (nose[0] - eye_center_x) / eye_distance


try:
    while True:
        success, frame = camera.read()

        if not success:
            print("ERROR: Cannot read camera.")
            break

        height, width = frame.shape[:2]
        detector.setInputSize((width, height))

        _, faces = detector.detect(frame)

        elapsed = time.time() - start_time
        remaining = max(0, challenge_timeout - int(elapsed))

        message = challenge
        color = (0, 255, 255)

        if faces is not None and len(faces) == 1:
            face = faces[0]
            x, y, w, h = face[:4].astype(int)

            yaw = estimate_yaw(face)

            if yaw is not None:
                if baseline_yaw is None:
                    baseline_yaw = yaw

                difference = yaw - baseline_yaw

                if challenge == "TURN LEFT":
                    movement_detected = difference < -0.18
                else:
                    movement_detected = difference > 0.18

                if movement_detected:
                    completed = True

            cv2.rectangle(
                frame,
                (x, y),
                (x + w, y + h),
                (0, 255, 0),
                2
            )

            if completed:
                message = "MOVEMENT DETECTED - TEST PASSED"
                color = (0, 255, 0)
            else:
                message = f"{challenge} | {remaining}s"

        elif faces is not None and len(faces) > 1:
            message = "ONLY ONE FACE ALLOWED"
            color = (0, 0, 255)
        else:
            message = "POSITION YOUR FACE IN CAMERA"
            color = (0, 0, 255)

        cv2.putText(
            frame,
            message,
            (10, 35),
            cv2.FONT_HERSHEY_SIMPLEX,
            0.65,
            color,
            2
        )

        cv2.imshow("ARGUS-LAN Liveness Test", frame)

        if completed:
            print("Movement detected. Development test passed.")
            cv2.waitKey(1500)
            break

        if elapsed >= challenge_timeout:
            print("Challenge timed out. Test failed.")
            break

        if cv2.waitKey(1) & 0xFF == ord("q"):
            break

finally:
    camera.release()
    cv2.destroyAllWindows()
