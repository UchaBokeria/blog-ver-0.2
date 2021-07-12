<?php 
    // session_start();
    // if(!isset($_SESSION["token"]))
    //     header("Location:../../../assets/wildcard.php"); 
?>
<div class="blog_admin-filter">
    <i class="material-icons" id="blog_create_new">add_circle_outline</i>

    <div class="blog_admin-filter-search">
        <input type="text" value="" placeholder="Suchen" id="blog_search_text">

        <div class="blog_post-type-select">
            <div data-type="1" id="blog_activated">Alle</div>
            <div data-type="2">öffentlich</div>
            <div data-type="3">Privat</div>
            <div data-type="4">Projekt</div>
        </div>

        <i class="fa fa-angle-down" id="blog_show_post_types"></i>
    </div>

    <input type="date" name="start_date" value="<?php echo date('Y-m-d'); ?>" id="blog_start_date">
    <input type="date" name="end_date" value="<?php echo date('Y-m-d'); ?>" id="blog_end_date">

    <button type="button" id="blog_filter">Filter</button>
</div>

<div id="admin-blog" >
    <div class="blog_exhibition-posts" data-id='$value["id"]'>
        <div class='blog_post-text'>
            <b data-id='$value["id"]'>$value["title"]</b>
            <p>$value["status"]</p>
            <p>$value["createdAt"]</p>
            <p data-id='$value["id"]'></p>
        </div>
        
        <div class="blog_post-edit-delete">
            <i class="material-icons blog_edit"  data-id='$value["id"]'>edit</i>
            <i class="material-icons blog_delete"  data-id='$value["id"]'>delete</i>
        </div>
        <i class="fa fa-angle-down blog_show_more"  data-id='$value["id"]'></i>
        
        <div class="blog_post_body" data-id='$value["id"]'>
         <!-- htmlspecialchars_decode($value['body']) -->
        </div>
    </div> 
</div>
<script>
    $(document).on("click", "#blog_filter", function () {
        var html_to_send = $("#admin-blog").html();
        console.log(html_to_send);
        param = new Object();
        param.act = "edit_post";
        param.class = "AdminBlog";
        param.html = html_to_send;
        console.log(param);
        $.ajax({
            url: "app/admin-side/admin-blog/admin-blog.action.php",
            data: param,
            dataType: "json",
            contentType: "html",
            success: function (response) {
                $("#admin-blog").html(response.content);
            }
        });
    });
    setTimeout(() => {

    }, 3000);
</script>

<!-- <div id="blog_edit-window"></div> 

<div id="blog_dialog"></div> -->

