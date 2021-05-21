<?php 
    session_start();
    if(!isset($_SESSION["token"]))
        header("Location:../../../assets/wildcard.php"); 
    include_once "../../module.php";

    $act = $_REQUEST["act"];

    $_SESSION['image'] = array();
    $_SESSION["tmp_img_numb"];
    $all_img_numb = $_SESSION["numb"];
    
    // test class
    class AdminBlog extends Model{
        private $User_id = null;
        private $Result = null;
        private $Error = null;
        private $param = null;
        public  $html = null;

        public function __construct(){
            $this->User_id = $_SESSION["user_id"];
            $this->html = "";
        }

        public function getPosts($post_id = ""){
            $detail_post = "";

            $this->param = array(["attr"=>$this->User_id,"type"=>PDO::PARAM_INT]);
            if($post_id != ""){
              array_push($this->param,["attr"=>$post_id,"type"=>PDO::PARAM_INT]);
              $detail_post .= " AND  posts.id = ? ";
            }
            
            $sql = "SELECT      posts.id,
                                accounts.profile_pic,
                                accounts.nickname,
                                posts.title,
                                posts.body,
                                posts.`desc`,
                                posts.createdAt,
                                `status`.title AS `status`,
                                posts.status_id AS `status_id`,
                                GROUP_CONCAT(files.dir) AS `path`
                      FROM      posts
                      JOIN      accounts ON posts.user_id = accounts.id
                      LEFT JOIN files ON files.post_id = posts.id
                      LEFT JOIN `status` ON `status`.id = posts.status_id AND `status`.activated = 1
                      WHERE     posts.activated = 1  AND posts.category_id = 2 AND posts.user_id = ? $detail_post
                      GROUP BY  posts.id
                      ORDER BY  posts.createdAt DESC ";

            $blogs = $this->get($sql,$this->param);
            $this->generate($blogs);
        }
        
        public function getEditPost(){
            $id = $this->User_id;
            $res = $this->getPosts($id);

            foreach ($res as $value) {
                $imgNumb = explode(",",$value["path"]);
                $img_counter += count($imgNumb);
                $_SESSION["numb"] = $img_counter;
                $result["content"].="<div class='blog_edit-window-ajax'  data-id=".$value['id']." >
                                        <i class='material-icons blog_close-ajax-edit' data-id=".$value['id']." >close</i>
                                        <h1 class='blog_edit_desc'>Description</h1>
                                        <h1 class='blog_edit_img'>Photo</h1>
                                        <input type='text' id='blog_new_title' name=".$value['title']." value='".$value['title']."'>
                                        
                                        <div class='blog_edit-post-type-select'>
                                            <i class='fa fa-angle-down ' id='blog_edit_post_types' style='top:1vh;'></i>";
                
                $selectedStatus = " hey i am empty cuz i was bron as error";
                $selectedStatus = " <div data-type='" .$value["status_id"]. "' id='blog_activated'>".$value["status"]."</div>";
                
                // get all statuses except this post status id
                $elseStatuses = $get->StatusList($value["status_id"]);
                foreach ($elseStatuses as  $status) {
                    $selectedStatus .= " <div data-type='" .$status["id"]. "' >".$status["title"]."</div>";
                }
                $result["content"].= $selectedStatus;                          
                $result["content"].="   </div>+
                                        <!-- CKEditor  -->
                                        <div class='blog_post_body_edit' data-id='".$value['id']."'>
                                            <div class='editor-head' data-id=".$value['id']."></div>
                                            <div class='editor-body' data-id=".$value['id']." id='blog_text_fix_cke'>" . htmlspecialchars_decode($value['body']) . "</div>
                                        </div>
                                        <div class='blog_edit_upload_image'>
                                            <div class='blog_upload_form'>  
                                            <form id='blog_mmmm' enctype='multipart/form-data'>                           
                                                <input type='file' id='blog_post_file' name='file[]' multiple>
                                                <label for='blog_post_file'><img src='assets/images/upload.png'></label>
                                            </form>
                                            </div>
                                            
                                            <div class='blog_images_output'>";
                                            for($i=0;$i != count($imgNumb); $i++){
                                                if(count($imgNumb) != 0){
                                                    if(file_exists("../../../assets/uploads/".$imgNumb[$i]) && $imgNumb[$i] != ""){
                                                        $result["content"].="<div class='blog_img_output_div' del-id='".($i+50)."'> 
                                                                                <img src='assets/uploads/".$imgNumb[$i]."' > 
                                                                                <i class='material-icons blog_delete_image' del-id='".($i+50)."' data-type='1' data-path='".$imgNumb[$i]."'>close</i>
                                                                            </div>";

                                                    }
                                                }
                                            }

                         $result["content"].=" </div>  
                                        </div>
                                        <button class='blog_save-button' type='button' id='".$value['id']."'>speicher</button>
                                        <br>
                                        <button class='blog_cancel-button' type='button'>abbrechen</button>
                                    </div>";
        }

        public function filterPosts(){
            $filterParameters = json_decode($_REQUEST["data"],true);
            $filter = " AND posts.category_id IN(?) AND posts.title LIKE ? AND posts.createdAt BETWEEN ? AND ?  AND posts.user_id = ? ";
            
            $this->param = array( 
                ["attr"=>2,  "type"=>PDO::PARAM_INT],
                ["attr"=>$this->User_id, "type"=>PDO::PARAM_INT],
                ["attr"=>'%' . $filterParameters["searchWord"] . '%', "type"=>PDO::PARAM_STR],
                ["attr"=>$filterParameters["start_date"] . " 00:00",  "type"=>PDO::PARAM_STR],
                ["attr"=>$filterParameters["end_date"] . " 23:59",    "type"=>PDO::PARAM_STR]
            );

            if($filterParameters["status"] != 1){
                array_push($this->param,["attr"=>$filterParameters["status"], "type"=>PDO::PARAM_INT]);
                $filter .= "  AND posts.status_id = ? ";
            }

            $sql = "SELECT      posts.id,
                                accounts.profile_pic,
                                accounts.nickname,
                                posts.title,
                                posts.body,
                                posts.`desc`,
                                posts.createdAt,
                                `status`.title AS `status`,
                                posts.status_id AS `status_id`,
                                GROUP_CONCAT(files.dir) AS `path`
                        FROM      posts
                        JOIN      accounts ON posts.user_id = accounts.id
                        LEFT JOIN files ON files.post_id = posts.id
                        LEFT JOIN `status` ON `status`.id = posts.status_id AND `status`.activated = 1
                        WHERE     posts.activated = 1 $filter
                        GROUP BY  posts.id
                        ORDER BY  posts.createdAt DESC ";

            $blogs = $this->get($sql,$this->param);
            $this->generate($blogs);
        }

        public function delete_tmp_folder(){
            $path = $_REQUEST["pathes"];
            for($i=0;$i!=count($path);$i++){
                for ($j=0; $j < count($path[$i]); $j++) { 
                    $dir = "../../../assets/uploads/tmp/".$path[$i][$j];
                    unlink($dir); 
                }
            }
        }

        public function delete_img(){
            $path = $_REQUEST["path"];
            $name = $_REQUEST["name"];

            unlink($path);
            $this->params = array(["attr"=>$name,"type"=> PDO::PARAM_STR ]);
        
            $sql = "DELETE FROM files WHERE dir = ?";
            
            $this->set($sql,$this->params);
        }

        public function add_post_img(){
            $dir = $_REQUEST["dir"];

            $lastId = $this->getAll("SELECT MAX(id) AS LastID FROM posts ");
            
            for($i=0;$i!=count($dir);$i++){
                for ($j=0; $j < count($dir[$i]); $j++) { 
                    rename("../../../assets/uploads/tmp/".$dir[$i][$j],"../../../assets/uploads/".$dir[$i][$j]);

                    $this->params = array(
                        ["attr"=>$dir[$i][$j],"type"=> PDO::PARAM_STR],
                        ["attr"=>$lastId,"type"=> PDO::PARAM_INT]
                    );
            
                    $sql = "INSERT INTO files(dir,post_id)
                            VALUE(?,?)";
                      
                    $this->set($sql,$this->params);
                }
            }
        }

        public function edit_post_img(){
            $dir = $_REQUEST["dir"];
            $id = $_REQUEST["id"];  

            for($i=0;$i!=count($dir);$i++){
                for ($j=0; $j < count($dir[$i]); $j++) { 
                    rename("../../../assets/uploads/tmp/".$dir[$i][$j],"../../../assets/uploads/".$dir[$i][$j]);

                    $this->params = array(
                        ["attr"=>$$dir[$i][$j],"type"=> PDO::PARAM_STR],
                        ["attr"=>$id,"type"=> PDO::PARAM_INT]
                    );
            
                    $sql = "INSERT INTO files(dir,post_id)
                            VALUE(?,?)";
                      
                    $this->set($sql,$this->params);
                }
            }
        }

        public function add_post(){
            $title = $_REQUEST["title"];
            $desc = "";
            $body = $_REQUEST["body"];
            $status_id = $_REQUEST["status_id"];
            $category_id = $_REQUEST["category_id"];

            $this->params = array(
                ["attr"=>$title,"type"=> PDO::PARAM_STR ],
                ["attr"=>htmlspecialchars($body),"type"=> PDO::PARAM_STR ],
                ["attr"=>htmlspecialchars($desc),"type"=> PDO::PARAM_STR ],
                ["attr"=>$this->user_id,"type"=> PDO::PARAM_INT],
                ["attr"=>$status_id,"type"=> PDO::PARAM_INT],
                ["attr"=>$category_id,"type"=> PDO::PARAM_INT]
            );
        
            $sql = "INSERT INTO posts 
                    SET `title` 			= ?,
                        `body`				= ?,
                        `desc` 				= ?,
                        `user_id` 		= ?,
                        `status_id` 	= ?,
                        `category_id` = ?,
                        `createdAt`	 	= NOW();";
        
            $this->set($sql,$this->params);



        }

        public function edit_post(){
            $title = $_REQUEST["title"];
            $desc = "";
            $body = $_REQUEST["body"];
            $post_id = $_REQUEST["post_id"];
            $status_id = $_REQUEST["status_id"];
            $category_id = $_REQUEST["category_id"];


            $this->params = array(
                ["attr"=>$title,"type"=> PDO::PARAM_STR ],
                ["attr"=>htmlspecialchars($body),"type"=> PDO::PARAM_STR ],
                ["attr"=>htmlspecialchars($desc),"type"=> PDO::PARAM_STR ],
                ["attr"=>$this->user_id,"type"=> PDO::PARAM_INT],
                ["attr"=>$status_id,"type"=> PDO::PARAM_INT],
                ["attr"=>$category_id,"type"=> PDO::PARAM_INT],
                ["attr"=>$post_id,"type"=> PDO::PARAM_INT]
            );
        
            $sql = "UPDATE  posts 
                        SET `title` 		= ?,
                            `body`			= ?,
                            `desc` 			= ?,
                            `user_id` 		= ?,
                            `status_id` 	= ?,
                            `category_id`   = ?,
                            `updatedAt`	 	= NOW()
                        WHERE id = ?";
        
            $this->set($sql,$this->params);
        }

        public function delete_post(){
            $id = $_REQUEST["id"];
            $restorable = true;

            $this->params = array( ["attr"=>$id,"type"=> PDO::PARAM_INT ] );

            if($restorable){
                $sql = "UPDATE posts 
                        SET `activated` 			= 0,
                            `updatedAt`	 	= NOW()
                        WHERE  id = ?;";
            }
            else
                $sql = "DELETE FROM posts WHERE id = ?";

            $this->set($sql,$this->params);
        }

        protected function generate($res){
            foreach ($res as $value) {
                $this->html .= " 
                    <div class='blog_exhibition-posts' data-id=".$value['id'].">
                        <div class='blog_post-text'>
                            <b data-id=".$value['id'].">".$value['title']."</b>
                            <p>".$value['status']."</p>
                            <p>".$value['createdAt']."</p>
                        </div>
                        
                        <div class='blog_post-edit-delete'>
                            <i class='material-icons blog_edit'  data-id='".$value['id']."'>edit</i>
                            <i class='material-icons blog_delete'  data-id=".$value['id'].">delete</i>
                        </div>
                        <i class='fa fa-angle-down blog_show_more'  data-id=".$value['id']."></i>
                        
                        <div class='blog_post_body' data-id=".$value['id'].">
                        ". htmlspecialchars_decode($value['body']) ."
                        </div>
                    </div> ";
            }

        }
    }

    $obj = new AdminBlog();
    $obj->getPosts();
    $obj->filterPosts();
    $obj->delete_tmp_folder();
    $obj->delete_img();
    $obj->add_post_img();
    $obj->edit_post_img();
    $obj->add_post();
    $obj->edit_post();
    $obj->delete_post();

    echo json_encode($obj->html);



    switch ($act) {
        case 'get_new':
            //esec html shi gadavides
                $result["content"] .="<div class='blog_edit-window-ajax' id='blog_newpost'>
                                        <i class='material-icons blog_close-ajax-edit' >close</i>

                                        <h1 class='blog_add_desc'>Description</h1>
                                        <h1 class='blog_add_img'>Photo</h1>

                                        <input type='text' placeholder='Erstelle neu' id='blog_new_title'>
                                        
                                        <div class='blog_edit-post-type-select-new'>
                                            <i class='fa fa-angle-down ' id='blog_edit_post_types-new' style='top:1vh;'></i>
                                            <div data-type='2' id='blog_activated'>öffentlich</div>
                                            <div data-type='3'>Privat</div>
                                            <div data-type='4'>Projekt</div>
                                        </div>
                                    
                                        <!-- CKEditor  -->
                                        <div class='blog_post_body_edit'>
                                            <div class='editor-head'></div>
                                            <div class='editor-body'></div>
                                        </div>

                                        <div class='blog_edit_upload_image'>
                                            <div class='blog_upload_form'>  
                                                <form id='blog_mmmm' enctype='multipart/form-data'>                           
                                                    <input type='file' id='blog_post_file' name='file[]' multiple>
                                                    <label for='blog_post_file'><img src='assets/images/upload.png'></label>
                                                </form>
                                            </div>

                                            <div class='blog_images_output'>
                                            </div>  
                                        </div>
                                        <button class='blog_add-save-button' type='button'>speicher</button>
                                        <br>
                                        <button class='blog_cancel-button' type='button'>abbrechen</button>
                                    </div>";
            
            
            break;
        case 'get_delete_dialog':
            // es html shi dabrundes ra unda request tyuila ixarjeba
                $result["content"] = "<p id='blog_delete_dialog_text'>Möchten Sie diesen Beitrag wirklich löschen?</p>
                                      <button id='blog_delete_dialog_yes'>Ja</button>
                                      <button id='blog_delete_dialog_no'>Nein</button>";
            break;



        case 'create_post':
            $title = $_REQUEST["title"];
            $body = $_REQUEST["body"];
            $desc = $_REQUEST["desc"];
            $status_id = $_REQUEST["status_id"];
            $category_id = $_REQUEST["category_id"];
            $set->createPost($title,htmlspecialchars($body),"",$user_id,$status_id,$category_id);
            break;
    
        case 'delete_post':
            $id = $_REQUEST["id"];
            $set->deletePost($id);
            break;
        case 'edit_post':            
            $title = $_REQUEST["title"];
            $desc = "";
            $body = $_REQUEST["body"];
            $post_id = $_REQUEST["id"];
            $status_id = $_REQUEST["status_id"];
            $category_id = $_REQUEST["category_id"];
            $set->editPost($post_id,$title,htmlspecialchars($body),htmlspecialchars($desc),$user_id,$status_id,$category_id);
            break;
        case 'add_post':
            $title = $_REQUEST["title"];
            $desc = "";
            $body = $_REQUEST["body"];
            $status_id = $_REQUEST["status_id"];
            $category_id = $_REQUEST["category_id"];
            $set->createPost($title,htmlspecialchars($body),htmlspecialchars($desc),$user_id,$status_id,$category_id);
            break;
        case 'tmp_upload':
            $img_counter = count($_FILES["file"]["name"]);
            $imgArra = array();

            $_SESSION["tmp_img_numb"] = count($_FILES["file"]["name"]);;

            $all_img_numb += $img_counter;
            $result["count"] = $all_img_numb;
            for($i=0;$i!=$img_counter;$i++){
                $name = $_FILES['file']['name'][$i];

                $dir = "../../../assets/uploads/tmp/".$name;

                $global_div = $dir;
                $imageFileType = pathinfo($dir,PATHINFO_EXTENSION);
                $imgType = strtolower(pathinfo($dir,PATHINFO_EXTENSION));   
            
                //type
                if($imgType != "jpg" && $imgType != "png" && $imgType != "jpeg" && $imgType != "gif"){
                    echo "This ile is not an image";
                }
                if(file_exists($dir)){
                    $withoutExtension = pathinfo($dir);
                    $dir = $withoutExtension["dirname"] . '/' . $withoutExtension["filename"] . $user_id .'.'. $withoutExtension["extension"];
           
                }
                if(move_uploaded_file($_FILES['file']['tmp_name'][$i],$dir)){
                    array_push($imgArra,$name);
                }
                
                $result["content"] .= "<div class='blog_img_output_div'  del-id='".$i."'> 
                                        <img src='assets/uploads/tmp/".$name."' data-path='".$name."' class='blog_test_img_gtxov' >
                                        <i class='material-icons blog_delete_image' data-type='2' del-id='".$i."' data-path='".$name."'>close</i>
                                    </div>";
            }

            $result["tmp_upload"] = $imgArra;
            break;
    








 