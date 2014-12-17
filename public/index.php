<?php

/******************************* LOADING & INITIALIZING BASE APPLICATION ****************************************/

// Configuration for error reporting, useful to show every little problem during development
error_reporting(E_ALL);
ini_set("display_errors", 1);
session_start();

// Load Composer's PSR-4 autoloader (necessary to load Slim, Mini etc.)
require '../vendor/autoload.php';

// Initialize Slim (the router/micro framework used)
$app = new \Slim\Slim();

/*****************  ******************/
$app->add(new \Slim\Middleware\SessionCookie(array('secret' => 'ecZQ2uj14tGf6vKi8')));

function authen(){
    if (isset($_SESSION['user'])) {
        return $_SESSION['user'];
    }else{
        header('Location: /login');
        exit;
    }
}

function validate(){
    return isset($_SESSION['user']) ? $_SESSION['user'] : false ;
}

function getFlash($name){
    return isset($_SESSION['slim.flash'][$name]) ? $_SESSION['slim.flash'][$name] : false ;
}
/*****************  ******************/

// and define the engine used for the view @see http://twig.sensiolabs.org
$app->view = new \Slim\Views\Twig();
$app->view->setTemplatesDirectory("../Mini/view");

/******************************************* THE CONFIGS *******************************************************/

// Configs for mode "development" (Slim's default), see the GitHub readme for details on setting the environment
$app->configureMode('development', function () use ($app) {

    // pre-application hook, performs stuff before real action happens @see http://docs.slimframework.com/#Hooks
    $app->hook('slim.before', function () use ($app) {

        // SASS-to-CSS compiler @see https://github.com/panique/php-sass
        SassCompiler::run("scss/", "css/");

        // CSS minifier @see https://github.com/matthiasmullie/minify
        $minifier = new MatthiasMullie\Minify\CSS('css/style.css');
        $minifier->minify('css/style.css');

        // JS minifier @see https://github.com/matthiasmullie/minify
        // DON'T overwrite your real .js files, always save into a different file
        //$minifier = new MatthiasMullie\Minify\JS('js/application.js');
        //$minifier->minify('js/application.minified.js');
    });

    // Set the configs for development environment
    $app->config(array(
        'debug' => true,
        'database' => array(
            'db_host' => 'localhost',
            'db_port' => '',
            'db_name' => 'workone',
            'db_user' => 'root',
            'db_pass' => '1234'
        )
    ));
});

// Configs for mode "production"
$app->configureMode('production', function () use ($app) {
    // Set the configs for production environment
    $app->config(array(
        'debug' => false,
        'database' => array(
            'db_host' => '',
            'db_port' => '',
            'db_name' => '',
            'db_user' => '',
            'db_pass' => ''
        )
    ));
});

/******************************************** THE MODEL ********************************************************/

// Initialize the model, pass the database configs. $model can now perform all methods from Mini\model\model.php
$model = new \Mini\Model\Model($app->config('database'));

// !! Change "private $db" to "public $db"
// $pmodel = new \Mini\Model\Product($app->config('database'));
// var_dump($pmodel->get_products());

$app->hook('slim.before.dispatch', function() use ($app) {
    $user = null;
    if (isset($_SESSION['user'])) {
        $user = $_SESSION['user'];
    }
    $app->view()->setData('user', $user);
});

/************************************ THE ROUTES / CONTROLLERS *************************************************/

// GET request on homepage, simply show the view template index.twig
$app->get('/', function () use ($app, $model) {
    $products = $model->get_products();
    $user = validate();
    $app->render('product/index.twig', array(
        'products' => $products,
        'title' => 'Home',
        'user' => $user,
        'error' => getFlash('error'),
    ));
});

/**********************************************************/
/************************* LOGIN **************************/
/**********************************************************/
$app->get('/login', function () use ($app) {
    if (validate()===false) {
        $app->render('login_form.twig', array(
            'error' => getFlash('error'),
        ));
    }else{
        $app->redirect('/user');
    }
});

/**********************************************************/
/************************ PRODUCT *************************/
/**********************************************************/
$app->group('/product', function () use ($app, $model) {

    $app->get('/', function () use ($app, $model) {
        $products = $model->get_products();
        $user = validate();
        $app->render('product/index.twig', array(
            'products' => $products,
            'title' => 'Product',
            'user' => $user,
            'error' => getFlash('error'),
        ));
    });

    $app->get('/form', 'authen', function () use ($app) {
        $app->render('product/form.twig', array(
            'id' => 0,
        ));
    });

    $app->get('/form/:id', function ($id = 0) use ($app, $model) {
        if ($id == 0) {
            $app->redirect('/product');
        }
        $item = $model->get_products($id);
        $app->render('product/form.twig', array(
            'id' => $id,
            'item' => $item,
        ));
    });

    $app->get('/detail/:id', function ($id = 0) use ($app, $model) {
        if ($id == 0) {
            $app->redirect('/product');
        }
        $user = validate();
        $item = $model->get_products($id);
        $comments = $model->get_comments($id);
        $app->render('product/detail.twig', array(
            'id' => $id,
            'item' => $item,
            'user' => $user,
            'comments' => $comments,
        ));
    });

    $app->post('/save', 'authen', function () use ($app, $model) {
        $user = validate();
        $data = array(
            'title' => $_POST['title'],
            'details' => $_POST['detail'],
            'user_id' => $user->id,
            'id' => $_POST['id'],
        );

        if ($_FILES['file']['error'] === 0) {
            $base_dir = dirname(__FILE__);
            if ($data['id'] != 0) {
                $file = $model->get_products($data['id']);
                $img_path = $base_dir.'/img/product/'.$file->preview;
                if (is_file($img_path)) {
                    unlink($img_path);
                }
            }

            $info = getimagesize($_FILES['file']['tmp_name']);
            if (preg_match('/.*\/(png|jpeg|jpg)/', $info['mime'])) {
                $file_name = uniqid().strrchr($_FILES['file']['name'], ".");
                $data['file_name'] = $file_name;
                move_uploaded_file($_FILES['file']['tmp_name'], $base_dir.'/img/product/'.$file_name);
            }
        }

        if ($data['id'] == 0) {
            $model->product_save($data);
        }else{

            $model->product_save($data);
        }
        $app->redirect('/product');
    });

    $app->get('/delete/:id', function ($id = 0) use ($app, $model) {
        if ($id == 0) {
            $app->redirect('/product');
        }

        $user = validate();
        if ($user->level == 1) {
            $app->flash('error', 'Invalid access level');
            $app->redirect('/');
        }

        $base_dir = dirname(__FILE__);
        $file = $model->get_products($id);
        $img_path = $base_dir.'/img/product/'.$file->preview;
        if (is_file($img_path)) {
            unlink($img_path);
        }

        $model->product_delete($id);
        $app->redirect('/product');
    });
});

/**********************************************************/
/************************** USER **************************/
/**********************************************************/
$app->group('/user', function () use ($app, $model) {

    $app->get('/', 'authen', function () use ($app, $model) {
        $user = validate();
        if ($user->level == 1) {
            $app->flash('error', 'Invalid access level');
            $app->redirect('/');
        }
        $users = $model->get_users();
        $app->render('user/index.twig', array(
            'users' => $users,
            'error' => getFlash('error'),
        ));
    });

    $app->get('/form', 'authen', function () use ($app) {
        $app->render('user/form.twig', array(
            'id' => 0,
            'user' => array(),
        ));
    });

    $app->get('/form/:id', 'authen', function ($id = 0) use ($app, $model) {
        if ($id === 0) {
            $app->redirect('/user');
        }

        $user = $model->get_users($id);
        $app->render('user/form.twig', array(
            'id' => $id,
            'user' => $user
        ));
    });

    $app->post('/save', 'authen', function () use ($app, $model) {

        $user = validate();
        if ($user->level == 1) {
            $app->flash('error', 'Invalid access level');
            $app->redirect('/');
        }

        $find_user = $model->find_by_username($_POST);
        if ($find_user !== false) {
            $app->flash('error', 'Username has been used');
            $app->redirect('/user');
        }

        $model->user_save($_POST);
        $app->redirect('/user');
    });

    $app->get('/delete/:id', 'authen', function ($id = 0) use ($app, $model) {
        if ($id === 0) {
            $app->flash('error', 'Invalid id');
            $app->redirect('/');
        }
        $user = validate();
        if ($user->level == 1) {
            $app->flash('error', 'Invalid access level');
            $app->redirect('/');
        }
        $model->user_delete($id);
        $app->redirect('/user');
    });

    $app->post('/login', function () use ($app, $model) {
        $login = $model->login_user($_POST);
        if ($login === false) {
            $app->flash('error', 'can not found this user :(');
            $app->redirect('/login');
        }else{
            $_SESSION['user'] = $login;
            $app->redirect('/');
        }
    });

    $app->get('/logout', function () use ($app) {
        unset($_SESSION['user']);
        $app->view()->setData('user', null);
        $app->redirect('/');
    });
});

/**********************************************************/
/********************* COMMENT ****************************/
/**********************************************************/
$app->group('/comment', function () use ($app, $model) {
    $app->post('/save', 'authen', function () use ($app, $model) {

        $user = validate();
        $_POST['user_id'] = $user->id;
        $model->comment_save($_POST);
        $app->redirect('/product/detail/'.$_POST['product_id']);
    });

    $app->get('/delete/:product_id/:id', 'authen', function ($product_id = 0, $id = 0) use ($app, $model) {
        if ($id == 0 || $product_id == 0) {
            $app->redirect('/');
        }
        $model->comment_delete($id);
        $app->redirect('/product/detail/'.$product_id);
    });
});

/**********************************************************/
/***************** SHOW CSS AND HTML **********************/
/**********************************************************/
$app->get('/showcase', function () use ($app) {
    $app->render('showcase.twig');
});

/**********************************************************/
/***************** CALCULATE AGE TO WEEK ******************/
/**********************************************************/
$app->get('/age', function () use ($app) {
    $app->render('age.twig');
});
$app->post('/age/cal', function () use ($app) {

    if (empty($_POST['year']) OR empty($_POST['month'] OR empty($_POST['day']))) {
        $app->contentType('application/json;charset=utf-8');
        echo json_encode(array('msg' => 'Please fill all input :)'));
        exit;
    }

    $date = new DateTime(date('Y-m-d'), new DateTimeZone('Asia/Bangkok'));
    $date1 = date_create($date->format('Y-m-d'));

    $position1 = $date1->format('w');

    $birth_date = new DateTime($_POST['year'].'-'.$_POST['month'].'-'.$_POST['day'], new DateTimeZone('Asia/Bangkok'));
    $date2 = date_create($birth_date->format('Y-m-d'));
    $date_txt = $birth_date->format('l');

    $position2 = $birth_date->format('w');

    $diff = date_diff($date1, $date2);
    $week_rows = round($diff->days / 7);

    if ($position1 > $position2) {
        $day_rows = $position1 - $position2;
    }else if($position1 < $position2){
        $day_rows = 7 - ($position2 - $position1);
    }else{
        $day_rows = 0;
    }

    $msg = 'You born on '.$date_txt.', now '.$week_rows.' weeks and '.$day_rows.' days passed';
    $app->contentType('application/json;charset=utf-8');
    echo json_encode(array('msg' => $msg));
});
/******************************************* RUN THE APP *******************************************************/

$app->run();
