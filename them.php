<?php
// Kết nối đến cơ sở dữ liệu
$conn = new mysqli('localhost', 'root', '', 'qlsv_nguyenquanghuy(21108)');
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Xử lý khi form được gửi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy giá trị từ form
    $id = isset($_POST['id']) ? $_POST['id'] : null;
    $fullname = $_POST['fullname'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $hometown = $_POST['hometown']; // Lấy giá trị quê quán từ form
    $level = $_POST['level'];
    $group = $_POST['group'];

    if ($id) {
        // Cập nhật thông tin sinh viên nếu có ID
        $update_sql = "UPDATE table_students 
                       SET fullname = ?, dob = ?, gender = ?, hometown = ?, level = ?, `group` = ? 
                       WHERE id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param('ssssiii', $fullname, $dob, $gender, $hometown, $level, $group, $id);
    } else {
        // Thêm sinh viên mới nếu không có ID
        $insert_sql = "INSERT INTO table_students (fullname, dob, gender, hometown, level, `group`) 
                       VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param('ssssii', $fullname, $dob, $gender, $hometown, $level, $group);
    }

    // Thực thi câu lệnh
    if ($stmt->execute()) {
        header('Location: index.php');
        exit();
    } else {
        echo "Lỗi: " . $stmt->error;
    }
}

// Lấy thông tin sinh viên nếu có ID
$student = null;
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $select_sql = "SELECT * FROM table_students WHERE id = ?";
    $stmt = $conn->prepare($select_sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($student) ? "Sửa sinh viên" : "Thêm sinh viên" ?></title>
    <link rel="stylesheet" href="style3.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h2><?= isset($student) ? "Sửa thông tin sinh viên" : "Thêm sinh viên" ?></h2>
        <form method="POST">
            <?php if (isset($student)): ?>
                <input type="hidden" name="id" value="<?= $student['id'] ?>">
            <?php endif; ?>

            <label for="fullname">Họ và Tên:</label><br>
            <input type="text" name="fullname" value="<?= $student['fullname'] ?? '' ?>" required><br><br>

            <label for="dob">Ngày Sinh:</label><br>
            <input type="date" name="dob" value="<?= $student['dob'] ?? '' ?>" required><br><br>

            <label for="gender">Giới tính:</label><br>
            <input type="radio" name="gender" value="1" <?= isset($student) && $student['gender'] == 1 ? 'checked' : '' ?>> Nam
            <input type="radio" name="gender" value="0" <?= isset($student) && $student['gender'] == 0 ? 'checked' : '' ?>> Nữ<br><br>

            <label for="hometown">Quê quán:</label><br>
            <input type="text" name="hometown" value="<?= $student['hometown'] ?? '' ?>" required><br><br>

            <label for="level">Trình độ học vấn:</label><br>
            <select name="level" required>
                <?php 
                $levels = ['Tiến sĩ',   'Thạc sĩ', 'Kỹ sư', 'Khác'];
                foreach ($levels as $index => $label): ?>
                    <option value="<?= $index ?>" <?= isset($student) && $student['level'] == $index ? 'selected' : '' ?>><?= $label ?></option>
                <?php endforeach; ?>
            </select><br><br>

            <label for="group">Nhóm:</label><br>
            <input type="number" name="group" value="<?= $student['group'] ?? '' ?>" required><br><br>

            <button type="submit"><i class="fa-solid fa-floppy-disk"></i> <?= isset($student) ? "Cập nhật" : "Lưu" ?></button>
        </form>
    </div>
</body>
</html>
