<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
//use yii\web\Controller;
use yii\rest\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Subscription;
use app\models\Product;
use app\models\Post;
use app\models\Category;
use app\models\SubCategory;
use app\models\ProductImages;
use app\models\Users;
use app\modules\api\models\ApiLoginForm;
use yii\web\ForbiddenHttpException;
use yii\web\UploadedFile;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }
    

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }
    
    public function beforeAction($action) { 
        Yii::$app->controller->enableCsrfValidation = false; 
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: PUT, GET, POST");
        header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
        return true;
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin1()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionLogin()
    { 
        $model = new ApiLoginForm();
        $request = Yii::$app->request;
        $params = json_decode($request->getRawBody());
        $model->attributes = (array)$params;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if ($model->login()) {
            $user = Yii::$app->user->identity;
            $user['status'] = 'success';
            return $user;
        } else {
            return [
                'status' => 'error'
            ];
        }
    }

    public function actionAddsubscription()
    { 
        $model = new Subscription();
        $request = Yii::$app->request;
        $params = json_decode($request->getRawBody());
        $model->attributes = (array)$params;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if ($model->add()) {
            $user = Yii::$app->user->identity;
            $user['status'] = 'success';
            return $user;
        } else {
            return [
                'status' => 'error'
            ];
        }
    }

    public function actionSubscription()
    { 
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $list = Subscription::find()->orderBy([ 'id' => SORT_DESC])->all();
        $data = [];
        $i = 1;
        foreach ($list as $key => $value) {
            $data[$key]['name'] = $i;
            $data[$key]['content'] = $value['email'];
            $i++;
        }
        return $data;
    }

    public function actionCategory()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $list = Category::find()->andWhere(['status' => '1'])->orderBy([ 'id' => SORT_DESC])->all();
        $data = [];
        $i = 1;
        foreach ($list as $key => $value) {
            $allPost = Post::find()->andWhere(['category_id' => $value['id'], 'status' => '1'])->orderBy([ 'id' => SORT_DESC])->all();
            $fileNamePath = 'https://images.unsplash.com/photo-1536329583941-14287ec6fc4e?ixid=MnwxMjA3fDB8MHxzZWFyY2h8MTF8fGRlc2lnbnxlbnwwfHwwfHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60';
            if(count($allPost) > 0){
                $fileNamePath =  Yii::$app->params['adminURL'].$allPost[0]['filename'];
            }
            $data[$key]['id'] = $value['id'];
            $data[$key]['name'] = $value['title'];
            $data[$key]['href'] = "#";
            $data[$key]['thumbnail'] = $fileNamePath;
            $data[$key]['count'] = count($allPost);
            $data[$key]['color'] = "indigo";
            $data[$key]['type'] = $value['type'];
            $i++;
        }
        return $data;
    }

    public function actionArticlecategory()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $list = Category::find()->andWhere([ 'type' => 'Article'])->orderBy([ 'id' => SORT_DESC])->all();
        $data = [];
        $i = 1;
        $fileNamePath = 'https://images.unsplash.com/photo-1536329583941-14287ec6fc4e?ixid=MnwxMjA3fDB8MHxzZWFyY2h8MTF8fGRlc2lnbnxlbnwwfHwwfHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60';
        $data[0]['id'] = 0;
        $data[0]['name'] = "All";
        $data[0]['href'] = "#";
        $data[0]['thumbnail'] = $fileNamePath;
        $data[0]['count'] = 0;
        $data[0]['color'] = "indigo";
        $data[0]['type'] = "Article";
        foreach ($list as $key => $value) {
            $allPost = Post::find()->andWhere(['category_id' => $value['id'], 'status' => '1'])->orderBy([ 'id' => SORT_DESC])->all();
            if(count($allPost) > 0){
                $fileNamePath =  Yii::$app->params['adminURL'].$allPost[0]['filename'];
            }
            $data[$i]['id'] = $value['id'];
            $data[$i]['name'] = $value['title'];
            $data[$i]['href'] = "#";
            $data[$i]['thumbnail'] = $fileNamePath;
            $data[$i]['count'] = count($allPost);
            $data[$i]['color'] = "indigo";
            $data[$i]['type'] = $value['type'];
            $i++;
        }
        return $data;
    }

    public function actionProductcategory()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $list = Category::find()->andWhere([ 'type' => 'Product'])->orderBy([ 'id' => SORT_DESC])->all();
        $data = [];
        $i = 1;
        $fileNamePath = 'https://images.unsplash.com/photo-1536329583941-14287ec6fc4e?ixid=MnwxMjA3fDB8MHxzZWFyY2h8MTF8fGRlc2lnbnxlbnwwfHwwfHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60';
        $data[0]['id'] = 0;
        $data[0]['name'] = "All";
        $data[0]['href'] = "#";
        $data[0]['thumbnail'] = $fileNamePath;
        $data[0]['count'] = 0;
        $data[0]['color'] = "indigo";
        $data[0]['type'] = "Article";
        foreach ($list as $key => $value) {
            $allPost = Post::find()->andWhere(['category_id' => $value['id'], 'status' => '1'])->orderBy([ 'id' => SORT_DESC])->all();
            if(count($allPost) > 0){
                $fileNamePath =  Yii::$app->params['adminURL'].$allPost[0]['filename'];
            }
            $data[$i]['id'] = $value['id'];
            $data[$i]['name'] = $value['title'];
            $data[$i]['href'] = "#";
            $data[$i]['thumbnail'] = $fileNamePath;
            $data[$i]['count'] = count($allPost);
            $data[$i]['color'] = "indigo";
            $data[$i]['type'] = $value['type'];
            $i++;
        }
        return $data;
    }

    public function actionArticlesubcategory()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $list = SubCategory::find()->joinWith(['category'])->andWhere(['type' => 'Article'])->orderBy([ 'id' => SORT_DESC])->all();
        $data = [];
        $i = 1;
        $data = [];
        $i = 1;
        $fileNamePath = 'https://images.unsplash.com/photo-1536329583941-14287ec6fc4e?ixid=MnwxMjA3fDB8MHxzZWFyY2h8MTF8fGRlc2lnbnxlbnwwfHwwfHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60';
        $data[0]['id'] = 0;
        $data[0]['name'] = "All";
        $data[0]['href'] = "#";
        $data[0]['thumbnail'] = $fileNamePath;
        $data[0]['count'] = 0;
        $data[0]['color'] = "indigo";
        $data[0]['type'] = "Article";
        foreach ($list as $key => $value) {
            $data[$i]['id'] = $value['id'];
            $data[$i]['name'] = $value['title'];
            $data[$i]['categoryName'] = $value->category->title;
            $data[$i]['categoryId'] = $value['category_id'];;
            $data[$i]['href'] = "#";
            $data[$i]['thumbnail'] = "https://images.unsplash.com/photo-1536329583941-14287ec6fc4e?ixid=MnwxMjA3fDB8MHxzZWFyY2h8MTF8fGRlc2lnbnxlbnwwfHwwfHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60";
            $data[$i]['count'] = Post::find()->andWhere(['category_id' => $value['id'], 'status' => '1'])->count();;
            $data[$i]['color'] = "indigo";
            $i++;
        }
        return $data;
    }

    public function actionProductsubcategory()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $list = SubCategory::find()->joinWith(['category'])->andWhere(['type' => 'Product'])->orderBy([ 'id' => SORT_DESC])->all();
        $data = [];
        $i = 1;
        $data = [];
        $i = 1;
        $fileNamePath = 'https://images.unsplash.com/photo-1536329583941-14287ec6fc4e?ixid=MnwxMjA3fDB8MHxzZWFyY2h8MTF8fGRlc2lnbnxlbnwwfHwwfHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60';
        $data[0]['id'] = 0;
        $data[0]['name'] = "All";
        $data[0]['href'] = "#";
        $data[0]['thumbnail'] = $fileNamePath;
        $data[0]['count'] = 0;
        $data[0]['color'] = "indigo";
        $data[0]['type'] = "Article";
        foreach ($list as $key => $value) {
            $data[$i]['id'] = $value['id'];
            $data[$i]['name'] = $value['title'];
            $data[$i]['categoryName'] = $value->category->title;
            $data[$i]['categoryId'] = $value['category_id'];;
            $data[$i]['href'] = "#";
            $data[$i]['thumbnail'] = "https://images.unsplash.com/photo-1536329583941-14287ec6fc4e?ixid=MnwxMjA3fDB8MHxzZWFyY2h8MTF8fGRlc2lnbnxlbnwwfHwwfHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60";
            $data[$i]['count'] = Post::find()->andWhere(['category_id' => $value['id'], 'status' => '1'])->count();;
            $data[$i]['color'] = "indigo";
            $i++;
        }
        return $data;
    }

    public function actionSearcproductcategory()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $list = Category::find()->andWhere([ 'type' => 'Product'])->orderBy([ 'id' => SORT_DESC])->all();
        $data = [];
        $i = 1;
        $fileNamePath = 'https://images.unsplash.com/photo-1536329583941-14287ec6fc4e?ixid=MnwxMjA3fDB8MHxzZWFyY2h8MTF8fGRlc2lnbnxlbnwwfHwwfHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60';
        $data[0]['id'] = 0;
        $data[0]['name'] = "All";
        $data[0]['href'] = "#";
        $data[0]['thumbnail'] = $fileNamePath;
        $data[0]['count'] = 0;
        $data[0]['color'] = "indigo";
        $data[0]['type'] = "Product";
        foreach ($list as $key => $value) {
            $allPost = Post::find()->andWhere(['category_id' => $value['id'], 'status' => '1'])->orderBy([ 'id' => SORT_DESC])->all();
            if(count($allPost) > 0){
                $fileNamePath =  Yii::$app->params['adminURL'].$allPost[0]['filename'];
            }
            $data[$i]['id'] = $value['id'];
            $data[$i]['name'] = $value['title'];
            $data[$i]['href'] = "#";
            $data[$i]['thumbnail'] = $fileNamePath;
            $data[$i]['count'] = count($allPost);
            $data[$i]['color'] = "indigo";
            $data[$i]['type'] = $value['type'];
            $i++;
        }
        return $data;
    }
    
    public function actionProduct()
    { 
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $list = Product::find()->joinWith(['category'])->orderBy([ 'id' => SORT_DESC, 'status' => '1'])->all();
        $data = [];
        $i = 1;
        foreach ($list as $key => $value) {
            // var_dump($value->category); exit;
            $data[$key]['index'] = $i;
            $data[$key]['id'] = $value['id'];
            $data[$key]['title'] = $value['title'];
            $data[$key]['desc'] = $value['content'];
            $data[$key]['date'] = $value['createddate'];
            $data[$key]['href'] = "#";
            $data[$key]['featuredImage'] = Yii::$app->params['adminURL'].$value['filename'];
            $data[$key]['commentCount'] = 0;
            $data[$key]['viewdCount'] = 0;
            $data[$key]['readingTime'] = 0;
            $data[$key]['postType'] = "standard";
            $data[$key]['categoriesId'] = [$value['category_id']];
            $data[$key]['bookmark'] = ["count" => 0,"isBookmarked" => false];
            $data[$key]['like'] = ["count" => 0,"isLiked" => false];
            $data[$key]['authorId'] = 1;
            $data[$key]['categoryName'] = $value->category->title;
            $data[$key]['category'] = $value['category_id'];
            $data[$key]['subcategory'] = $value['sub_category_id'];
            $i++;
        }
        return $data;
    }

    public function actionArticle()
    { 
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $list = Post::find()->andWhere(['post.status' => 1])->joinWith(['category'])->orderBy([ 'id' => SORT_DESC])->all();
        $data = [];
        $i = 1;
        if(count($list) > 0){
            foreach ($list as $key => $value) {
                // var_dump($value->category); exit;
                $data[$key]['index'] = $i;
                $data[$key]['id'] = $value['id'];
                $data[$key]['title'] = $value['title'];
                $data[$key]['desc'] = $value['content'];
                $data[$key]['date'] = $value['createddate'];
                $data[$key]['href'] = "#";
                $data[$key]['featuredImage'] = Yii::$app->params['adminURL'].$value['filename'];
                $data[$key]['commentCount'] = 0;
                $data[$key]['viewdCount'] = 0;
                $data[$key]['readingTime'] = 0;
                $data[$key]['postType'] = "standard";
                $data[$key]['categoriesId'] = [$value['category_id']];
                $data[$key]['bookmark'] = ["count" => 0,"isBookmarked" => false];
                $data[$key]['like'] = ["count" => 0,"isLiked" => false];
                $data[$key]['authorId'] = 1;
                $data[$key]['categoryName'] = $value->category->title;
                $data[$key]['category'] = $value['category_id'];
                $data[$key]['subcategory'] = $value['sub_category_id'];
                $i++;
            }
        }
        
        return $data;
    }

    public function actionArticledash()
    { 
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $list = Post::find()->joinWith(['category'])->orderBy([ 'id' => SORT_DESC])->all();
        $data = [];
        $i = 1;
        foreach ($list as $key => $value) {
            // var_dump($value->category); exit;
            $data[$key]['index'] = $i;
            $data[$key]['id'] = $value['id'];
            $data[$key]['title'] = $value['title'];
            $data[$key]['desc'] = $value['content'];
            $data[$key]['date'] = $value['createddate'];
            $data[$key]['href'] = "#";
            $data[$key]['featuredImage'] = Yii::$app->params['adminURL'].$value['filename'];
            $data[$key]['commentCount'] = 0;
            $data[$key]['viewdCount'] = 0;
            $data[$key]['readingTime'] = 0;
            $data[$key]['postType'] = "standard";
            $data[$key]['categoriesId'] = [$value['category_id']];
            $data[$key]['bookmark'] = ["count" => 0,"isBookmarked" => false];
            $data[$key]['like'] = ["count" => 0,"isLiked" => false];
            $data[$key]['authorId'] = 1;
            $data[$key]['categoryName'] = $value->category->title;
            $data[$key]['category'] = $value['category_id'];
            $data[$key]['subcategory'] = $value['sub_category_id'];
            $data[$key]['status'] = ( $value['status'] == 1) ? 'Active' : "Draft";
            $i++;
        }
        return $data;
    }

    public function actionProductview()
    { 
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $params = json_decode($request->getRawBody());
        $id = $params->id->id;
        $list = Product::findOne(['id' => $id, 'status'=>'1']);
        $content = $list->content;
        $contentNew = $list->content_new;
        $content = preg_replace('/<span[^>]+\>|<\/span>/i', '', $content);
        $content = preg_replace('/<div[^>]+\>|<\/div>/i', '', $content);
        $content = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $content);
        $contentNew = preg_replace('/<span[^>]+\>|<\/span>/i', '', $contentNew);
        $contentNew = preg_replace('/<div[^>]+\>|<\/div>/i', '', $contentNew);
        $contentNew = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $contentNew);
        $data['index'] = 1;
        $data['title'] = $list->title;
        $data['desc'] = $content;
        $data['descNew'] = $contentNew;
        $data['price'] = $list->price;
        $data['date'] = "May 20, 2021";
        $data['id'] = $list->id;
        $data['href'] = "#";
        $data['featuredImage'] = Yii::$app->params['adminURL'].$list->filename;
        $data['commentCount'] = 0;
        $data['viewdCount'] = 0;
        $data['readingTime'] = 0;
        $data['postType'] = "standard";
        $data['categoriesId'] = [$list->category_id];
        $data['bookmark'] = ["count" => 0,"isBookmarked" => false];
        $data['like'] = ["count" => 0,"isLiked" => false];
        $data['authorId'] = 1;
        
        $imagesList = $list->images;
        $namewith = '';
        $allFiles = [];
        if(count($imagesList) > 0){
            $j = 0;
            foreach($imagesList as $key => $img){
                $comma = ",";
                if($key === 0)
                    $comma = "";
                $namewith = $namewith.$comma.str_replace("postimages/","",$img['filename']);
                $allFiles[$key]['images'] = Yii::$app->params['adminURL'].$img['filename'];
                $allFiles[$key]['id'] = $j++;
            }
        }
        $data['filePath'] = $namewith;
        $data['fileList'] = $allFiles;

            
        return $data;
    }

    public function actionArticleview()
    { 
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $params = json_decode($request->getRawBody());
        $id = $params->id->id;
        
        //var_dump($id);exit;
        $list = Post::findOne(['id' => $id]);
        $content = $list->content;
        $content = preg_replace('/<span[^>]+\>|<\/span>/i', '', $content);
        $content = preg_replace('/<div[^>]+\>|<\/div>/i', '', $content);
        $content = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $content);
        $data['index'] = 1;
        $data['title'] = $list->title;
        $data['desc'] = $content;
        $data['date'] = "May 20, 2021";
        $data['id'] = $list->id;
        $data['href'] = "#";
        $data['featuredImage'] = Yii::$app->params['adminURL'].$list->filename;
        $data['commentCount'] = 0;
        $data['viewdCount'] = 0;
        $data['readingTime'] = 0;
        $data['postType'] = "standard";
        $data['categoriesId'] = [$list->category_id];
        $data['category'] = $list->category_id;
        $data['subcategory'] = $list->sub_category_id;
        $data['bookmark'] = ["count" => 0,"isBookmarked" => false];
        $data['like'] = ["count" => 0,"isLiked" => false];
        $data['authorId'] = 1;
        $data['filePath'] = $list->filename;
            
        return $data;
    }

    public function actionAddpost()
    { 
        $model = new Post();
        $headers = array("Content-Type:multipart/form-data");
        $model->attributes = Yii::$app->request->post();
        $target_dir = "postimages/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $filename = $_FILES["image"]["tmp_name"];
        if($filename !== ''){
            move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
            $model->filename = $target_file;
        }
        //var_dump($model->attributes);exit;
        if ($model->add()) {
            $user = Yii::$app->user->identity;
            $user['status'] = 'success';
            return $user;
        } else {
            return [
                'status' => 'error'
            ];
        }
    }

    public function actionUpdatepost()
    { 
        $model = new Post();
        $headers = array("Content-Type:multipart/form-data");
        $request = Yii::$app->request->post();
        $target_dir = "postimages/";
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if(isset($_FILES["image"])){
            $target_file = $target_dir . basename($_FILES["image"]["name"]);
            $filename = $_FILES["image"]["tmp_name"];
            if($filename !== ''){
                move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
                $model->filename = $target_file;
            }
        }
        $id = $request['id'];
        $title = $request['title'];
        $category_id = $request['category_id'];
        $content = $request['content'];
        Yii::$app->db->createCommand("UPDATE post SET title=:title,category_id=:category_id,content=:content WHERE id=:id")
        ->bindValue(':id', $id)
        ->bindValue(':title', $title)
        ->bindValue(':category_id', $category_id)
        ->bindValue(':content', $content)
        ->execute();
        return [
            'status' => 'success'
        ];
    }

    public function actionAddproduct()
    { 
        $model = new Product();
        $headers = array("Content-Type:multipart/form-data");
        $model->attributes = Yii::$app->request->post();
        $target_dir = "postimages/";       
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $imagesList = [];
        foreach($_FILES['image']['name'] as $i => $name){
            $filename = $_FILES['image']['tmp_name'][$i];
            $target_file = $target_dir . basename($_FILES['image']['name'][$i]);
            $error = $_FILES['image']['error'][$i];
            $size = $_FILES['image']['size'][$i];
            $type = $_FILES['image']['type'][$i];
            if($filename !== ''){
                move_uploaded_file($filename, $target_file);
                array_push($imagesList, $target_file);
                $model->filename = $target_file;
            }
        }
        if ($model->add()) {
            if(count($imagesList) > 0){
                foreach($imagesList as $i => $imagename){
                    $modelImg = new ProductImages();
                    $modelImg->product_id = $model->id;
                    $modelImg->filename = $imagename;
                    if ($modelImg->add()) {
                    }
                }
            }
            $user['status'] = 'success';
            return $user;
        } else {
            return [
                'status' => 'error'
            ];
        }
    }

    public function actionFileupload()
    { 
        $headers = array("Content-Type:multipart/form-data");
        $target_dir = "editorimage/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $filename = $_FILES["image"]["tmp_name"];
        $result = [
            'status' => 'error',
            "filePath" => ''
        ];
        if($filename !== ''){
            if(move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)){
                $result = [
                    'status' => 'success',
                    'filePath' => Yii::$app->params['adminURL'].$target_file,
                ];
            }
        }
        
        return  $result;
    }

    public function actionDeletepost()
    { 
        $model = new Post();
        $request = Yii::$app->request;
        $params = json_decode($request->getRawBody());
        $id = $params->id;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $model = Post::find()->where(['id' => $id, 'status' => '1'])->one();
        $result = [
            'status' => 'error',
            'id' => $id
        ];
        if($model->delete()){
            $result = [
                'status' => 'success',
                'postList' => $this->actionArticle()
            ];
        }
        return $result;
    }

    public function actionDeleteproduct()
    { 
        $model = new Product();
        $request = Yii::$app->request;
        $params = json_decode($request->getRawBody());
        $id = $params->id;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $model = Product::find()->where(['id' => $id, 'status' => '1'])->one();
        $result = [
            'status' => 'error',
            'id' => $id
        ];
        if($model->delete()){
            $result = [
                'status' => 'success',
                'postList' => $this->actionProduct()
            ];
        }
        return $result;
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionSubmitcontact()
    {
        $request = Yii::$app->request;
        $params = json_decode($request->getRawBody());
        $attributes = (array)$params;
        //var_dump($attributes);exit;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $email = "info@healthbeautybank.com";
        $replyEmail = $attributes['email'];
        $name = $attributes['name'];
        $subject = "Contact Form";
        $body = $attributes['desc'];
        Yii::$app->mailer->compose()
                ->setTo($email)
                ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']])
                ->setReplyTo([$replyEmail => $name])
                ->setSubject($subject)
                ->setTextBody($body)
                ->send();
        $user['status'] = 'success';
        return $user;
        
    }


    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionSignup()
    {
        $request = Yii::$app->request;
        $params = json_decode($request->getRawBody());
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $model = new Users();
        $model->attributes = (array)$params;
        if ($model->add()) {
        }
        $user['status'] = 'success';
        return $user;
        
    }

    public function actionAddcategory()
    { 
        $model = new Category();
        $request = Yii::$app->request;
        $params = json_decode($request->getRawBody());
        $model->attributes = (array)$params;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if ($model->add()) {
            return [
                'status' => 'success'
            ];
        } else {
            return [
                'status' => 'error'
            ];
        }
    }

    public function actionEditcategory()
    { 
        $model = new Category();
        $request = Yii::$app->request;
        $params = json_decode($request->getRawBody());
        $model->attributes = (array)$params;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        Yii::$app->db->createCommand("UPDATE category SET title=:title,type=:type WHERE id=:id")
        ->bindValue(':id', $params->id)
        ->bindValue(':title', $params->title)
        ->bindValue(':type', $params->type)
        ->execute();
        return [
            'status' => 'success'
        ];
    }

    public function actionDeletecategory()
    { 
        $request = Yii::$app->request;
        $params = json_decode($request->getRawBody());
        $id = $params->id;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        // $model = Category::find()->where(['id' => $id, 'status' => '1'])->one();
        // $result = [
        //     'status' => 'error',
        //     'id' => $id
        // ];
        // if($model->delete()){
        //     $result = [
        //         'status' => 'success',
        //         'categoryList' => $this->actionCategory()
        //     ];
        // }
        // return $result;
        Yii::$app->db->createCommand("UPDATE category SET status='0' WHERE id=:id")
        ->bindValue(':id', $params->id)
        ->execute();
        $result = [
            'status' => 'success',
            'categoryList' => $this->actionCategory()
        ];
        return $result;
    }

    public function actionSubcategory()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $list = SubCategory::find()->joinWith(['category'])->andWhere(['sub_category.status' => '1'])->orderBy([ 'id' => SORT_DESC])->all();
        $data = [];
        $i = 1;
        foreach ($list as $key => $value) {
            $data[$key]['id'] = $value['id'];
            $data[$key]['name'] = $value['title'];
            $data[$key]['categoryName'] = $value->category->title;
            $data[$key]['categoryId'] = $value['category_id'];;
            $data[$key]['href'] = "#";
            $data[$key]['thumbnail'] = "https://images.unsplash.com/photo-1536329583941-14287ec6fc4e?ixid=MnwxMjA3fDB8MHxzZWFyY2h8MTF8fGRlc2lnbnxlbnwwfHwwfHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60";
            $data[$key]['count'] = Post::find()->andWhere(['category_id' => $value['id'], 'status' => '1'])->count();;
            $data[$key]['color'] = "indigo";
            $i++;
        }
        return $data;
    }

    public function actionEditsubcategory()
    { 
        $model = new Category();
        $request = Yii::$app->request;
        $params = json_decode($request->getRawBody());
        $model->attributes = (array)$params;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        Yii::$app->db->createCommand("UPDATE sub_category SET title=:title,category_id=:category_id WHERE id=:id")
        ->bindValue(':id', $params->id)
        ->bindValue(':title', $params->title)
        ->bindValue(':category_id', $params->category_id)
        ->execute();
        return [
            'status' => 'success'
        ];
    }

    public function actionAddsubcategory()
    { 
        $model = new SubCategory();
        $request = Yii::$app->request;
        $params = json_decode($request->getRawBody());
        $model->attributes = (array)$params;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if ($model->add()) {
            return [
                'status' => 'success'
            ];
        } else {
            return [
                'status' => 'error'
            ];
        }
    }

    public function actionDeletesubcategory()
    { 
        $request = Yii::$app->request;
        $params = json_decode($request->getRawBody());
        $id = $params->id;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $model = SubCategory::find()->where(['id' => $id, 'status' => '1'])->one();
        $result = [
            'status' => 'error',
            'id' => $id
        ];
        // if($model->delete()){
        //     $result = [
        //         'status' => 'success',
        //         'categoryList' => $this->actionArticle()
        //     ];
        // }
        Yii::$app->db->createCommand("UPDATE sub_category SET status='0' WHERE id=:id")
        ->bindValue(':id', $params->id)
        ->execute();
        return [
            'status' => 'success',
            'categoryList' => $this->actionSubcategory()
        ];
        return $result;
    }
}
