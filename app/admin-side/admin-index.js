var router =
{
    outlet: "admin-content",
    children: [
        {
            path:     'app/admin-side/admin-home/admin-home',
            fileName: 'admin-home',
            firstAct: 'getPosts',
            route:    'AdminHome',
            routerLink:'home',
            outlet:   'admin-home',
            id:1
        },
        {
            path:     'app/admin-side/admin-blog/admin-blog',
            fileName: 'admin-blog',
            firstAct: 'getPosts',
            route:    'AdminBlog',
            routerLink:'blog',
            outlet:   'admin-blog',
            id:2
        },
        {
            path:     'app/admin-side/admin-profile/admin-profile',
            fileName: 'admin-profile',
            firstAct: 'getPosts',
            route: 'AdminProfile',
            routerLink:'profile',
            outlet:   'admin-profile',
            id:3
        }
    ]
};
class PageLoader {
    defaultPage = null;
    component = null;

    constructor(defRoute = null) {
        if (defRoute == null)
            this.defaultPage = router.children[0].routerLink;
        else
            this.defaultPage = defRoute;
        
        this.Load(this.defaultPage);
    }

    Load(route) {
        this.component = router.children.find(obj => obj.routerLink == route);

        $.ajax({
            url: this.component.path + ".php",
            data: this.component,
            dataType: "html",
            type: "POST",
            success: function (HTML) {
                $("#" + router.outlet).html(HTML);
                console.log(route + " Has Been Loaded ");
            },
            error: function (textStatus, errorThrown) {
                alert("404");
                console.log(textStatus);
                console.log(errorThrown);
            }
        });
    }
}

var asdas = new PageLoader("blog");