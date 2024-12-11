<?php
// Kết nối đến cơ sở dữ liệu
$conn = new mysqli('localhost', 'root', '', 'qlsv_nguyenquanghuy(21108)'); // Tạo một kết nối mới tới cơ sở dữ liệu với các tham số: host, username, password, database.
if ($conn->connect_error) { // Kiểm tra kết nối có bị lỗi không
    die("Kết nối thất bại: " . $conn->connect_error); // Nếu có lỗi, kết thúc chương trình và hiển thị thông báo lỗi.
}

// Lấy dữ liệu tìm kiếm từ URL nếu có (dùng phương thức GET)
$search = isset($_GET['search']) ? $_GET['search'] : ''; // Nếu 'search' không tồn tại, gán giá trị mặc định là chuỗi rỗng.

// Câu lệnh SQL để lấy danh sách sinh viên có tên hoặc quê quán khớp với từ khóa tìm kiếm
$sql = "SELECT * FROM table_Students WHERE fullname LIKE ? OR hometown LIKE ?";
$stmt = $conn->prepare($sql); // Chuẩn bị câu truy vấn an toàn (prepared statement) để tránh lỗi SQL Injection.
$searchTerm = "%" . $search . "%"; // Định dạng chuỗi tìm kiếm bằng cách thêm ký tự wildcard `%`.
$stmt->bind_param("ss", $searchTerm, $searchTerm); // Gán giá trị biến `$searchTerm` vào câu truy vấn cho cả hai trường.
$stmt->execute(); // Thực thi câu truy vấn.
$result = $stmt->get_result(); // Lấy kết quả truy vấn dưới dạng tập kết quả.

// Hàm định dạng giới tính từ giá trị số sang dạng văn bản
function formatGender($gender) {
    return $gender == 1 ? 'Nam' : 'Nữ'; // Nếu `$gender` là 1 thì trả về 'Nam', ngược lại là 'Nữ'.
}

// Hàm định dạng trình độ học vấn từ giá trị số sang tên cụ thể
function formatLevel($level) {
    $levels = ['Tiến sĩ', 'Thạc sĩ', 'Kỹ sư', 'Khác']; // Mảng các giá trị tương ứng với từng cấp trình độ.
    return isset($levels[$level]) ? $levels[$level] : 'Không rõ'; // Kiểm tra xem `$level` có hợp lệ không, nếu không trả về 'Không rõ'.
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách sinh viên</title>
    <!-- Liên kết file CSS -->
    <link rel="stylesheet" href="style.css">
    <!-- Liên kết Font Awesome để sử dụng icon -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Danh sách sinh viên</h1>
        <!-- Form tìm kiếm -->
        <form method="GET" action="index.php" class="search-form">
            <input type="text" name="search" placeholder="Tìm kiếm theo tên hoặc quê quán..." class="search-input">
            <!-- Nút tìm kiếm -->
            <button type="submit" class="search-btn">
                <i class="fa-solid fa-magnifying-glass"></i> Tìm kiếm
            </button>
            <!-- Nút thêm sinh viên -->
            <a href="them.php" class="btn-add">
                <i class="fa-solid fa-user-plus"></i> Thêm sinh viên
            </a>
        </form>

        <!-- Bảng hiển thị danh sách sinh viên -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th><i class="fa-solid fa-pen fa-xs"></i> Họ và Tên</th>
                    <th>Ngày Sinh</th>
                    <th>Giới Tính</th>
                    <th><i class="fa-solid fa-location-pin fa-xs"></i> Quê Quán</th>
                    <th><i class="fa-solid fa-graduation-cap fa-sm"></i> Trình Độ Học Vấn</th>
                    <th><i class="fa-solid fa-user-group fa-sm"></i> Nhóm</th>
                    <th><i class="fa-solid fa-arrow-pointer fa-sm"></i> Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): // Kiểm tra nếu có dữ liệu trả về ?>
                    <?php while ($row = $result->fetch_assoc()): // Lặp qua từng dòng dữ liệu ?>
                        <tr>
                            <td><?= $row['id'] ?></td> <!-- Hiển thị ID -->
                            <td><?= $row['fullname'] ?></td> <!-- Hiển thị Họ và Tên -->
                            <td><?= date("d/m/Y", strtotime($row['dob'])) ?></td> <!-- Hiển thị Ngày sinh, định dạng ngày tháng -->
                            <td><?= formatGender($row['gender']) ?></td> <!-- Hiển thị Giới tính -->
                            <td><?= $row['hometown'] ?></td> <!-- Hiển thị Quê quán -->
                            <td><?= formatLevel($row['level']) ?></td> <!-- Hiển thị Trình độ học vấn -->
                            <td>Nhóm <?= $row['group'] ?></td> <!-- Hiển thị Nhóm -->
                            <td>
                                <!-- Các nút thao tác -->
                                <a class="btn" href="sua.php?id=<?= $row['id'] ?>">
                                    <i class="fa-solid fa-pen fa-2xs"></i> Sửa
                                </a>
                                <a class="btn btn-delete" href="xoa.php?id=<?= $row['id'] ?>">
                                    <i class="fa-solid fa-trash fa-2xs"></i> Xóa
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: // Nếu không có dữ liệu ?>
                    <tr>
                        <td colspan="8">Không có sinh viên nào phù hợp với tìm kiếm!</td> <!-- Thông báo không tìm thấy -->
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
$stmt->close(); // Đóng statement sau khi sử dụng.
$conn->close(); // Đóng kết nối cơ sở dữ liệu.
?>
