<?php
// Kết nối đến cơ sở dữ liệu
$conn = new mysqli('localhost', 'root', '', 'qlsv_nguyenquanghuy(21108)');
// Tạo đối tượng kết nối tới cơ sở dữ liệu MySQL với các tham số:
// Máy chủ 'localhost', tài khoản 'root', mật khẩu rỗng, và tên cơ sở dữ liệu 'qlsv_hovaten'.
if ($conn->connect_error) {
    // Nếu kết nối thất bại, dừng chương trình và thông báo lỗi
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Lấy ID sinh viên cần sửa
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Chuyển đổi ID sang kiểu số nguyên để tránh lỗi SQL Injection.

    // Sử dụng prepared statement để lấy thông tin sinh viên theo ID
    $stmt = $conn->prepare("SELECT * FROM table_Students WHERE id = ?");
    $stmt->bind_param("i", $id); // Gắn tham số ID vào câu lệnh
    $stmt->execute(); // Thực thi câu lệnh SQL
    $result = $stmt->get_result(); // Lấy kết quả truy vấn

    // Kiểm tra nếu sinh viên tồn tại
    if ($result->num_rows > 0) {
        $student = $result->fetch_assoc(); // Lấy thông tin sinh viên
    } else {
        die("Không tìm thấy sinh viên để sửa.");
    }

    $stmt->close(); // Đóng statement
} else {
    die("Không có ID sinh viên được cung cấp.");
}

// Cập nhật dữ liệu sinh viên khi người dùng gửi form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy thông tin từ form gửi lên
    $fullname = htmlspecialchars(trim($_POST['fullname'])); // Tên sinh viên
    $dob = htmlspecialchars(trim($_POST['dob'])); // Ngày sinh
    $gender = intval($_POST['gender']); // Giới tính
    $hometown = htmlspecialchars(trim($_POST['hometown'])); // Quê quán
    $level = intval($_POST['level']); // Trình độ học vấn
    $group = intval($_POST['group']); // Nhóm

    // Kiểm tra dữ liệu nhập vào
    if (empty($fullname) || empty($dob) || empty($hometown) || $group < 0) {
        echo "Vui lòng điền đầy đủ thông tin và nhập giá trị hợp lệ.";
    } else {
        // Sử dụng prepared statement để cập nhật dữ liệu vào cơ sở dữ liệu
        $update_sql = "UPDATE table_Students SET fullname = ?, dob = ?, gender = ?, hometown = ?, level = ?, `group` = ? WHERE id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("ssisiii", $fullname, $dob, $gender, $hometown, $level, $group, $id);

        // Thực thi câu lệnh cập nhật
        if ($stmt->execute()) {
            // Nếu cập nhật thành công, chuyển hướng về trang danh sách sinh viên
            header('Location: index.php');
            exit();
        } else {
            echo "Lỗi cập nhật: " . $conn->error;
        }

        $stmt->close(); // Đóng statement
    }
}

$conn->close(); // Đóng kết nối cơ sở dữ liệu
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa sinh viên</title>
    <link rel="stylesheet" href="style2.css"> 
    <!-- Liên kết tệp CSS để định dạng trang web -->
    <link href="path/to/font-awesome/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
</head>
<body>
    <form method="POST">
        <h2>Sửa thông tin sinh viên</h2>

        <!-- Trường nhập Họ và Tên -->
        <label for="fullname">Họ và Tên:</label><br>
        <input type="text" name="fullname" value="<?= htmlspecialchars($student['fullname']) ?>" required><br><br>
        <!-- Hiển thị giá trị hiện tại của sinh viên trong ô input -->

        <!-- Trường nhập Ngày sinh -->
        <label for="dob">Ngày Sinh:</label><br>
        <input type="date" name="dob" value="<?= htmlspecialchars($student['dob']) ?>" required><br><br>
        <!-- Hiển thị ngày sinh hiện tại của sinh viên trong ô input -->

        <!-- Trường chọn Giới tính -->
        <label for="gender">Giới tính:</label><br>
        <input type="radio" name="gender" value="1" <?= $student['gender'] == 1 ? 'checked' : '' ?>> Nam
        <input type="radio" name="gender" value="0" <?= $student['gender'] == 0 ? 'checked' : '' ?>> Nữ<br><br>
        <!-- Hiển thị giới tính hiện tại của sinh viên (Nam hoặc Nữ) -->

        <!-- Trường nhập Quê quán -->
        <label for="hometown">Quê quán:</label><br>
        <input type="text" name="hometown" value="<?= htmlspecialchars($student['hometown']) ?>" required><br><br>
        <!-- Hiển thị quê quán hiện tại của sinh viên trong ô input -->

        <!-- Trường chọn Trình độ học vấn -->
        <label for="level">Trình độ học vấn:</label><br>
        <select name="level" required>
            <option value="0" <?= $student['level'] == 0 ? 'selected' : '' ?>>Tiến sĩ</option>
            <option value="1" <?= $student['level'] == 1 ? 'selected' : '' ?>>Thạc sĩ</option>
            <option value="2" <?= $student['level'] == 2 ? 'selected' : '' ?>>Kỹ sư</option>
            <option value="3" <?= $student['level'] == 3 ? 'selected' : '' ?>>Khác</option>
        </select><br><br>
        <!-- Hiển thị trình độ học vấn hiện tại của sinh viên -->

        <!-- Trường nhập Nhóm -->
        <label for="group">Nhóm:</label><br>
        <input type="number" name="group" value="<?= htmlspecialchars($student['group']) ?>" required><br><br>
        <!-- Hiển thị nhóm hiện tại của sinh viên trong ô input -->

        <!-- Nút gửi form -->
        <button type="submit"><i class="fa-solid fa-floppy-disk"></i> Cập nhật</button>
    </form>
</body>
</html>
