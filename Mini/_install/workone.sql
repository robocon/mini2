-- Adminer 4.1.0 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `comments`;
CREATE TABLE `comments` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`product_id` int(11) NOT NULL,
`detail` text NOT NULL,
`user_id` int(11) NOT NULL,
`post_on` datetime NOT NULL,
PRIMARY KEY (`id`),
KEY `user_id` (`user_id`),
KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `comments` (`id`, `product_id`, `detail`, `user_id`, `post_on`) VALUES
(6,	8,	'อันนี้คิดว่าเป็นเรื่องที่ควรโดนนะครับ',	1,	'2014-12-17 13:02:30'),
(7,	8,	'sony ภาพลักษณ์ด้านความปลอดภัยป่นปี้ ไม่เหลือชิ้นดีแล้ว',	6,	'2014-12-17 13:02:49');

DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`title` varchar(255) NOT NULL,
`details` text NOT NULL,
`preview` varchar(255) NOT NULL,
`user_id` int(11) NOT NULL,
`post_on` datetime NOT NULL,
PRIMARY KEY (`id`),
KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `products` (`id`, `title`, `details`, `preview`, `user_id`, `post_on`) VALUES
(7,	'Facebook ทดสอบฟีเจอร์ขายของในกลุ่ม',	'ฟีเจอร์นี้จะอยู่เหนือช่องอัพเดตสเตตัส เขียนว่า Sell Something ซึ่งถ้ากดแล้วก็จะมีช่องให้กรอกรายละเอีบด เช่น ขายอะไร ราคาเท่าไหร่ คำอธิบายสินค้า และช่องให้ใส่รูปสินค้า รวมถึงวิธีการส่ง ซึ่งเป็นฟีเจอร์ที่น่าสนใจสำหรับพ่อค้าแม่ขายใน Facebook อยู่มาก แต่ยังเปิดให้ทดสอบในวงจำกัด ก็ต้องมารอดูว่าจะเปิดให้ใช้กันทั่วโลกเมื่อไหร่\r\nสามารถดูตัวอย่างของที่ขายผ่าน Facebook ผ่านฟีเจอร์ดังกล่าวได้ท้ายเบรก\r\n\r\nที่มา - The Next Web',	'54917eb508995.png',	1,	'2014-12-17 13:01:41'),
(8,	'อีกหนึ่งข่าวร้ายของ Sony Pictures เมื่อโดนอดีตพนักงานฟ้องร้องฐานหละหลวมเรื่องเก็บรักษาข้อมูล',	'ความเสียหายที่เกิดจากการถูกแฮคระบบคอมพิวเตอร์ของ Sony Pictures นั้นไม่เพียงทำให้ตัวบริษัทเองตกอยู่ในความเสี่ยงเท่านั้น แต่ข้อมูลส่วนบุคคลจำนวนกมากที่ถูกแฮคไปด้วยย่อมหมายถึงความเสียหายที่เกิดกับตัวบุคคลเหล่านั้นเองด้วยเช่นกัน และสำหรับอดีตพนักงานส่วนหนึ่งของ Sony Pictures เห็นว่าอดีตนายจ้างควรรับผิดชอบในเรื่องนี้ จึงได้ยื่นฟ้องร้องต่อศาลให้เอาผิดกับสตูดิโอดังฐานหละหลวมเรื่องมาตรการเก็บรักษาข้อมูลส่วนบุคคลที่อยู่ในความรับผิดชอบ\r\nกลุ่มอดีตพนักงานของ Sony Pictures ได้ส่งตัวแทนด้านกฎหมายเข้ายื่นคำฟ้องคดีนี้ต่อศาลแขวงใน California เป็นเอกสารยาว 45 หน้า โดยยกเอาความเสียหายเกี่ยวกับการถูกขโมยข้อมูลส่วนบุคคลของพนักงานและอดีตพนักงานกว่า 47,000 คน ทั้งหมายเลขประกันสังคม, ที่อยู่ตามทะเบียนบ้าน, หมายเลขโทรศัพท์, ไฟล์จากการสแกนหนังสือเดินทางและวีซ่า, ผลการประเมินการทำงาน รวมถึงข้อมูลบันทึกด้านสุขภาพ\r\nอดีตพนักงานกล่าวว่า Sony (พูดรวมทั้งกลุ่ม) ควรจะมีระบบรักษาความปลอดภัยของข้อมูลที่ดีกว่านี้ โดยเฉพาะอย่างยิ่งหลังจากที่เคยถูกแฮคเกอร์เจาะระบบของ PSN ครั้งใหญ่ในปี 2011 ยังไม่นับสัญญาณอันตรายที่แสดงให้เห็นว่าระบบมีจุดอ่อนอีกหลายครั้ง\r\nสำหรับข้อเรียกร้องในการฟ้องครั้งนี้ คือต้องการให้ Sony รับผิดชอบค่าใช้จ่ายในการดำเนินการต่างๆ เพื่อป้องกันการจารกรรมข้อมูลส่วนบุคคลเป็นระยะเวลา 5 ปี (ปีละประมาณ 700 ดอลลาร์) ตลอดจนความเสียหายที่อาจเกิดขึ้นกับบัญชีธนาคารและการใช้บัตรเครดิต\r\nการฟ้องร้องในครั้งนี้ยังมีความต้องการจะรวบรวมโจทก์ผู้เสียหายจากการที่ระบบของ Sony ถูกแฮค ทั้งในครั้งนี้และครั้งอื่นที่มีมาก่อนหน้า (ตัวอย่างเช่นที่ PSN ถูกเจาะ) เพื่อจะดำเนินคดีเป็นกลุ่มร่วมกันฟ้องด้วย\r\n\r\nที่มา - CNET, The Verge',	'54917ed62705d.png',	1,	'2014-12-17 13:02:14');

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`username` varchar(255) NOT NULL,
`password` varchar(255) NOT NULL,
`email` varchar(255) NOT NULL,
`level` tinyint(4) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `users` (`id`, `username`, `password`, `email`, `level`) VALUES
(1,	'admin',	'03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4',	'roboconk@gmail.com',	99),
(6,	'admin2',	'03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4',	'admin@demomail.com',	1);

-- 2014-12-17 13:03:10
