<?

$_SESSION['core_secret_number']=rand(1000,9999);
header("Content-type: image/png");

$width = 120; // Ширина рисунка
$height = 30;  // Высота рисунка
$k = 15.7; // Коэффициент увеличения/уменьшения картинки
$step = 11; // Шаг сетки

// создаем изображение
$im=imagecreate($width, $height);

// Выделяем цвет фона (белый)
$w=imagecolorallocate($im, 255, 255, 255);

// Выделяем цвет для более темных помех (темно-серый)
$g2=imagecolorallocate($im, 64,64,64);

// Выделяем четыре случайных темных цвета для символов
$d1 = 0;
$d2 = 250;
$cl1=imagecolorallocate($im,rand($d1,$d2),rand($d1,$d2),rand($d1,$d2));
$cl2=imagecolorallocate($im,rand($d1,$d2),rand($d1,$d2),rand($d1,$d2));
$cl3=imagecolorallocate($im,rand($d1,$d2),rand($d1,$d2),rand($d1,$d2));
$cl4=imagecolorallocate($im,rand($d1,$d2),rand($d1,$d2),rand($d1,$d2));

// Рисуем сетку
$d1 = 110;
$d2 = 180;
for ($i = -1; $i <= $width; $i += $step) {
	// Выделяем цвет для фона (светло-серый)
	$g1 = imagecolorallocate($im, rand($d1,$d2), rand($d1,$d2), rand($d1,$d2));
	imageline($im, $i, 0, $i, $height, $g1);
}
for ($i = -1; $i <= $height; $i += $step) {
	// Выделяем цвет для фона (светло-серый)
	$g1 = imagecolorallocate($im, rand($d1,$d2), rand($d1,$d2), rand($d1,$d2));
	imageline($im, 0, $i, $width, $i, $g1);
}
    
$hh = $height/2 - 5;

$font_file_mas = array();

$font_file_mas[] = 'css_js/fonts/MonotonRegular.ttf';
$font_file_mas[] = 'css_js/fonts/tahoma.ttf';
$font_file_mas[] = 'css_js/fonts/tahoma_bold.ttf';
$font_file_mas[] = 'css_js/fonts/EwertRegular.ttf';

$mas_l = count($font_file_mas);

$angle = 50;
// Выводим каждую цифру по отдельности, немного смещая случайным образом
ImageTTFText($im, rand(15,21), rand(-$angle,$angle), 15+rand(0,8), 22+rand(-2,2), $cl1, $font_file_mas[rand(0, $mas_l-1)], substr($_SESSION["core_secret_number"],0,1));
ImageTTFText($im, rand(15,21), rand(-$angle,$angle), $width/3+rand(-5,5), 22+rand(-2,2), $cl2, $font_file_mas[rand(0, $mas_l-1)], substr($_SESSION["core_secret_number"],1,1));
ImageTTFText($im, rand(15,21), rand(-$angle,$angle), $width/2+rand(-5,5), 22+rand(-2,2), $cl3, $font_file_mas[rand(0, $mas_l-1)], substr($_SESSION["core_secret_number"],2,1));
ImageTTFText($im, rand(15,21), rand(-$angle,$angle), ($width*3/4)+rand(-7,7), 22+rand(-2,2), $cl4, $font_file_mas[rand(0, $mas_l-1)], substr($_SESSION["core_secret_number"],3,1));

// Выводим пару случайных линий тесного цвета, прямо поверх символов.
for ($i=0;$i<4;$i++)
	imageline($im,rand(0,$width),rand(0,$height),rand(0,$width),rand(0,$height),$g2);

// Создаем новое изображение, увеличенного размера
$im1=imagecreatetruecolor($width*$k, $height*$k);

// Копируем изображение с изменением размеров в большую сторону
imagecopyresized($im1, $im, 0, 0, 0, 0, $width*$k, $height*$k, $width, $height);

// Создаем новое изображение, нормального размера
$im2=imagecreatetruecolor($width,$height);

// Копируем изображение с изменением размеров в меньшую сторону
imagecopyresampled($im2, $im1, 0, 0, 0, 0, $width, $height, $width*$k, $height*$k);

// Генерируем изображение
imagepng($im2);

// Освобождаем память
imagedestroy($im2);
imagedestroy($im1);
imagedestroy($im);

die;
?>