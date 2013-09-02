mshop
=====
<?php

YPi::interfaces('control');

class default_controller extends Controller implements Control {

public function ErrorPage() {}

public function main() {?

 phpinfo();

}

}; ?>

控制器类名为控制器文件名+controller，例如：控制器文件名为default，控制器类名为default_controller。

所有的控制器都从 Controller 派生，接受 Control 进行约束。

每个控制器必需包含有 ErrorPage 和 main 两个方法。

ErrorPage方法，可以为每个控制器定义不同的错误显示页面。

main方法是控制器的默认方法。
