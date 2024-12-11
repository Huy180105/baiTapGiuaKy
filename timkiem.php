<?php
// Kết nối đến cơ sở dữ liệu
$conn = new mysqli('localhost', 'root', '', 'qlsv_nguyenquanghuy');
// Tạo đối tượng kết nối tới cơ sở dữ liệu MySQL với các tham số:
// Máy chủ 'localhost', tài khoản 'root', mật khẩu rỗng, và tên cơ sở dữ liệu 'qlsv_hovaten'.
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
    // Nếu kết nối thất bại, chương trình sẽ dừng và thông báo lỗi.
}

// Khai báo biến để lưu kết quả tìm kiếm
$search_result = [];
// Mảng này sẽ lưu trữ các kết quả tìm kiếm nếu có.

if (isset($_POST['search'])) {
    // Kiểm tra nếu nút "search" đã được nhấn.
    $search_term = $_POST['search_term'];
    // Lấy giá trị từ ô nhập liệu người dùng nhập.

    // Câu truy vấn tìm kiếm với điều kiện tìm theo tên hoặc quê quán.
    $sql = "SELECT * FROM table_Students WHERE fullname LIKE ? OR hometown LIKE ?";
    $stmt = $conn->prepare($sql);
    // Sử dụng prepared statement để đảm bảo an toàn SQL.

    $search_param = "%" . $search_term . "%";
    // Thêm ký tự `%` vào trước và sau từ khóa tìm kiếm để sử dụng trong lệnh LIKE.
    $stmt->bind_param("ss", $search_param, $search_param);
    // Gắn tham số tìm kiếm vào câu truy vấn.

    // Thực thi truy vấn
    $stmt->execute();
    $result = $stmt->get_result();
    // Lấy kết quả của truy vấn.

    // Lưu tất cả kết quả vào mảng
    $search_result = $result->fetch_all(MYSQLI_ASSOC);
    // Chuyển đổi kết quả truy vấn thành mảng kết hợp để dễ xử lý.

    // Đóng statement
    $stmt->close();
    // Giải phóng bộ nhớ cho prepared statement.
}

// Đóng kết nối
$conn->close();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tìm kiếm sinh viên</title>
    <link rel="stylesheet" href="style2.css">
    <!-- Kết nối tới tệp CSS để định dạng giao diện -->
</head>
<body>
    <form method="POST">
        <!-- Form để người dùng nhập từ khóa tìm kiếm -->
        <h2>Tìm kiếm sinh viên</h2>
        <label for="search_term">Tên học viên hoặc Quê quán:</label><br>
        <input type="text" name="search_term" required>
        <!-- Ô nhập liệu cho từ khóa tìm kiếm -->
        <button type="submit" name="search">Tìm kiếm</button>
        <!-- Nút gửi form để thực hiện tìm kiếm -->
    </form>

    <?php if (isset($_POST['search'])): ?>
        <!-- Kiểm tra nếu người dùng đã nhấn nút tìm kiếm -->
        <?php if (count($search_result) > 0): ?>
            <!-- Nếu có kết quả trả về từ cơ sở dữ liệu -->
            <h3>Kết quả tìm kiếm:</h3>
            <table>
                <!-- Tạo bảng để hiển thị danh sách sinh viên -->
                <tr>
                    <th>ID</th>
                    <th>Họ và Tên</th>
                    <th>Ngày Sinh</th>
                    <th>Giới Tính</th>
                    <th>Quê Quán</th>
                    <th>Trình Độ</th>
                    <th>Nhóm</th>
                </tr>
                <?php foreach ($search_result as $student): ?>
                    <!-- Lặp qua từng sinh viên trong kết quả tìm kiếm -->
                    <tr>
                        <td><?= htmlspecialchars($student['id']) ?></td>
                        <!-- Hiển thị ID sinh viên -->
                        <td><?= htmlspecialchars($student['fullname']) ?></td>
                        <!-- Hiển thị họ và tên sinh viên -->
                        <td><?= date("d/m/Y", strtotime($student['dob'])) ?></td>
                        <!-- Hiển thị ngày sinh dưới định dạng dd/mm/yyyy -->
                        <td><?= $student['gender'] == 1 ? 'Nam' : 'Nữ' ?></td>
                        <!-- Hiển thị giới tính, 1 là Nam, 0 là Nữ -->
                        <td><?= htmlspecialchars($student['hometown']) ?></td>
                        <!-- Hiển thị quê quán sinh viên -->
                        <td>
                            <?php
                            // Hiển thị trình độ học vấn dựa trên giá trị số.
                            switch ($student['level']) {
                                case 0: echo "Tiến sĩ"; break;
                                case 1: echo "Thạc sĩ"; break;
                                case 2: echo "Kỹ sư"; break;
                                case 3: echo "Khác"; break;
                                default: echo "Không rõ";
                                // Nếu không xác định được trình độ, hiển thị "Không rõ".
                            }
                            ?>
                        </td>
                        <td><?= htmlspecialchars($student['group']) ?></td>
                        <!-- Hiển thị nhóm sinh viên -->
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <!-- Nếu không tìm thấy kết quả -->
            <p>Không tìm thấy sinh viên nào phù hợp với tìm kiếm!</p>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>
