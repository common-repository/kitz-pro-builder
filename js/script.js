jQuery(document).ready(function ($) {

    
	
   //jQuery("#toplevel_page_bloxx-connect ul.wp-submenu li.wp-first-item").remove();

    var get_type= bloxxapi.enable;
    if(get_type=="plugin_activated"){
     	$(".builder_inner_dropable .show_clone_html").addClass("et-l");   
    }
    
    
    $(".wrapContent").click(function () {
        if ($(window).width() > 600) {
            $(".open-sidebar").removeClass("active");
            $(".switch-sidebar").removeClass("active");
            $("#left_project").removeAttr("style");
            $("#leftCategorySidebar").removeAttr("style");
            $("#leftCategorySidebar").removeClass('sidebar-in');
            $(".builder_posts").css({
                'overflow-y': 'scroll',
                'left': '360px'
            });
            $(".builder_posts").hide();

            // hide layouts
            $("#leftCategorySidebar_layouts").removeClass('sidebar-in');
            $("#leftCategorySidebar_layouts").removeAttr("style");
            $(".open-sidebar-layouts").removeClass('active');

        } else {
            //$(".mob-open-sidebar").removeClass("active");
            $(".mob-switch-sidebar").removeClass("active");
            $("#mobCategoryTabs").hide();
            $("#mobProjectTabs").hide();
        }
    });

 

    $("li.builder_layout_exit .exit_builder").click(function (event) {
        event.preventDefault();
        var $this = $(this);
        Swal.fire({
            title: 'Are you sure?',
            text: "You want to exit from Kitz Pro Builder Builder",
            icon: 'warning',
            showCancelButton: true,
            //showDenyButton: true,
            confirmButtonColor: '#000',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Exit',            
            cancelButtonText: "Cancel",
        }).then((result) => {
            if (result.isConfirmed) {
                var meta_type = "kitz_disable";
                var page_id = $this.attr('data-id');
                update_bloxx_metas(page_id, meta_type, $this);
            } 
        });
    });


    
// ak code starts
    $("body").on("mousedown", ".builder_inner_dragpost_sel .builder-dragpost-sel .show_clone_img, .assign_headfooter_sel", function (event) {
        event.preventDefault();
        var $this = $(this);
        //Enable New Page meta and disable old page meta
       // var meta_type = "enable";
        var data_id = $this.attr('data-id');

        var usertype = $this.parent().attr('data-usertype');
        var sectiontype = $this.parent().attr('data-sectiontype');

        var buttons = $('<div>')
        .append(createButton('Ok', function() {
           swal.close();
           console.log('ok'); 
        }));

        var user_id = jQuery('#get_user_id_bloxx_client').val();
        var section_id = $this.parent().attr('id');
        console.log('section_id=>'+section_id);
        console.log('usertype=>'+usertype);
        console.log('sectiontype=>'+sectiontype);
       // console.log('section_id1=>'+section_id);

       		// sectiontype 1 = Free, 2= Premium
       		if(sectiontype=='2' && usertype=='free'){
       			Swal.fire({
                        html: buttons,
                        title: "Please upgrade your plan to use premium sections",
                        text: "",
                        showCancelButton: true,
                        cancelButtonText: "NO THANKS",
                        showConfirmButton: false,
                       // confirmButtonColor: '#000',
                        icon: "error"
                    });
       		}
       				


       // bloxx_check_frontend_page_section_type_jquery_ajax(section_id,user_id, $this);
    });

    /*
    $("body").on("mousedown", ".builder_inner_dragpost_sel .assign_headfooter_sel", function (event) {
        event.preventDefault();
        var $this = $(this);
        //Enable New Page meta and disable old page meta
       // var meta_type = "enable";
        var data_id = $this.attr('data-id');

        var usertype = $this.attr('data-usertype');
        var sectiontype = $this.attr('data-sectiontype');

        var buttons = $('<div>')
        .append(createButton('Ok', function() {
           swal.close();
           console.log('ok'); 
        }));

        var user_id = jQuery('#get_user_id_bloxx_client').val();
        var section_id = $this.parent().attr('id');
        console.log('section_id=>'+section_id);
        console.log('usertype=>'+usertype);
        console.log('sectiontype=>'+sectiontype);
       // console.log('section_id1=>'+section_id);

       		// sectiontype 1 = Free, 2= Premium
       		if(sectiontype=='2' && usertype=='free'){
       			Swal.fire({
                        html: buttons,
                        title: "Please upgrade your plan to use premium sections",
                        text: "",
                        showCancelButton: true,
                        cancelButtonText: "NO THANKS",
                        showConfirmButton: false,
                       // confirmButtonColor: '#000',
                        icon: "error"
                    });
       		}
       				


       // bloxx_check_frontend_page_section_type_jquery_ajax(section_id,user_id, $this);
    });
 */

     //Meta Boxes enable/disable
    function bloxx_check_frontend_page_section_type_jquery_ajax(section_id,user_id, $this) {

        var buttons = $('<div>')
        .append(createButton('Ok', function() {
           swal.close();
           console.log('ok'); 
        }));

        var ajax_url = bloxx.ajax_url;
        $.ajax({
            type: "POST",
            url: ajax_url,
            dataType: "json",
            data: {
                'action': 'bloxx_check_frontend_page_section_type_ajax',
                'section_id': section_id,
                "_nonce": bloxx.ajax_nonce,
                'user_id' : user_id
              
            },
            beforeSend: function () {
                var get_text = $this.html();
                $this.html(get_text + ' <i class="fa fa-spinner fa-spin"></i>');
            },
            success: function (resp) {
                if (resp.code == 200) {
                	// disable the drag section by removing builder-dragpost class

                	$('#builder_inner_dragpost_'+section_id+'.builder_inner_dragpost').disableSelection();
                	$('#builder_inner_dragpost_'+section_id+'.connectedSortable').disableSelection();
                	$('#builder_inner_dragpost_'+section_id+'.ui-sortable').disableSelection();
                	$('#builder_inner_dragpost_'+section_id).disableSelection();

                	$('#'+section_id).disableSelection();


                	jQuery('#'+section_id).removeClass('builder-dragpost');
                	jQuery('#builder_inner_dragpost_'+section_id).removeClass('builder_inner_dragpost');
                	jQuery('#builder_inner_dragpost_'+section_id).removeClass('connectedSortable');
                	jQuery('#builder_inner_dragpost_'+section_id).removeClass('ui-sortable');
                	

                     Swal.fire({
                        html: buttons,
                        title: "Please upgrade your plan to use premium sections",
                        text: "",
                        showCancelButton: true,
                        cancelButtonText: "NO THANKS",
                        showConfirmButton: false,
                       // confirmButtonColor: '#000',
                        icon: "error"
                    });

                    //window.location.href = $this.attr('href');
                    //console.log($this.attr('href'));
                } else {
                    //alert(resp.message);
                }
            },
            error: function () {
                Swal.fire({
                    title: "Error!",
                    text: "Please try again later",
                    confirmButtonColor: '#000',
                    icon: "error"
                });
            }
        });
    }

    function createButton(text, cb) {
      return $("<a class='the_plan_button_client_popup' target='_blank' href='https://app.divikitz.com/plans'>UPGRADE</a>");
    }

// ak code ends




    $("body").on("click", ".websites_pages li a.other_pages", function (event) {
        event.preventDefault();
        var $this = $(this);
        //Enable New Page meta and disable old page meta
        var meta_type = "enable";
        var page_id = $this.attr('data-id');
        var oldpageid = $this.attr('data-nm');
        update_bloxx_metas_switch_page(page_id, meta_type, oldpageid, $this);
    });

    //Meta Boxes enable/disable
    function update_bloxx_metas_switch_page(page_id, meta_type, oldpageid, $this) {
        var ajax_url = bloxx.ajax_url;
        $.ajax({
            type: "POST",
            url: ajax_url,
            dataType: "json",
            data: {
                'action': 'kitz_switch_metabox',
                'post_id': page_id,
                'old_page': oldpageid,
                "_nonce": bloxx.ajax_nonce,
                'meta_type': meta_type
            },
            beforeSend: function () {
                var get_text = $this.html();
                $this.html(get_text + ' <i class="fa fa-spinner fa-spin"></i>');
            },
            success: function (resp) {
                if (resp.code == 200) {
                    window.location.href = $this.attr('href');
                    //console.log($this.attr('href'));
                } else {
                    alert(resp.message);
                }
            },
            error: function () {
                Swal.fire({
                    title: "Error!",
                    text: "Please try again later",
                    confirmButtonColor: '#000',
                    icon: "error"
                });
            }
        });
    }


    function update_bloxx_metas(page_id, meta_type, $this) {
        var ajax_url = bloxx.ajax_url;
        $.ajax({
            type: "POST",
            url: ajax_url,
            dataType: "json",
            data: {
                'action': 'kitz_update_metabox',
                'post_id': page_id,
                "_nonce": bloxx.ajax_nonce,
                'meta_type': meta_type
            },
            beforeSend: function () {
                $this.html('<i class="fa fa-spinner fa-spin"></i>');
            },
            success: function (resp) {
                if (resp.code == 200) {
                    window.location.href = $this.attr('href');
                } else {
                    alert(resp.message);
                }
            },
            error: function () {
                Swal.fire({
                    title: "Error!",
                    text: "Please try again later",
                    confirmButtonColor: '#000',
                    icon: "error"
                });
            }
        });
    }



    $("body").on("click", ".mob-switch-sidebar", function () {
        $(".mob-open-sidebar.active").trigger("click");
        if ($(this).hasClass("active")) {
            $("#mobProjectTabs").attr("style", "display:none;");
            $(this).removeClass("active");
        } else {
            $("#mobProjectTabs").attr("style", "display:block;");
            $(this).addClass("active");
        }

    });

    $("body").on("click", ".switch-sidebar", function () {
        var $this=$(this);
        $(".open-sidebar.active a").trigger("click");
        $(".open-sidebar").removeClass("active");
        $(".open-sidebar-layouts").removeClass("active");

        var i=1;
        $(".left-list li").each(function(){
            if(i!=1){
                var get_old_image= $(this).find("a").attr('data-old-img');
                $(this).find("img").attr("src", get_old_image);
            }
            i++;
        });

        var get_img=$this.find("a").attr('data-img');
        $this.find("img").attr("src", get_img);


        // hide layouts
        $("#leftCategorySidebar_layouts").removeClass('sidebar-in');
        $("#leftCategorySidebar_layouts").removeAttr("style");
        $(".open-sidebar-layouts").removeClass('active');

      
        if ($(this).hasClass("active")) {
            $("#left_project").removeAttr("style");
            $(this).removeClass("active");
        } else {
            $("#left_project").attr("style", "left:60px");
            $(this).addClass("active");
        }

    });

   

    $(document).on("click", "#leftCategorySidebar ul.builder_categories li a.builder_cats", function () {
        var $this = $(this);
        var get_id = $this.attr('id');
        $("ul.builder_categories li a.builder_cats").removeClass('builder_cat_active');
        $(this).addClass("builder_cat_active");
        //load_sections(get_id, $this);
        //return false;

        if ($(".sections_lists #cat_post_" + get_id).length == 0) {
            load_sections(get_id, $this);
        } else {
            $(".sections_lists .builder_posts").hide();
            $(".sections_lists #cat_post_" + get_id).css({
                'position': 'fixed',
                'overflow-y': 'scroll',
                'left': '360px'
            });

            // $(".sections_lists #cat_post_" + get_id).show("slide", {
            //     direction: "left"
            // }, 800);
            $(".sections_lists #cat_post_" + get_id).show();
        }
    });


    $(document).on("click", "#leftCategorySidebar_layouts ul.builder_categories li a.builder_cats", function () {
        var $this = $(this);
        var get_id = $this.attr('id');
        $("ul.builder_categories li a.builder_cats").removeClass('builder_cat_active');
        $(this).addClass("builder_cat_active");
        var service_type_id = $('#service_type_id').val();
        if(service_type_id!=''){
            service_type_id = service_type_id;
        }else{
            service_type_id = '0';
        }

        if ($(".layouts_lists #cat_post_" + get_id).length == 0) {
            load_layouts(get_id, $this,service_type_id);
        } else {
            $(".layouts_lists .builder_posts").hide();
            $(".layouts_lists #cat_post_" + get_id).css({
                'position': 'fixed',
                'overflow-y': 'scroll',
                'left': '360px' 
            });

            $(".layouts_lists #cat_post_" + get_id).show("slide", {
                direction: "left"
            }, 800);
        }
    });

    
    

    // Load Layout Industries terms From API Ajax When clicking on open-sidebar-layouts class
    
    $(document).on("click", ".open-sidebar-layouts a", function (e) {
        var $this = $(this);
        var current_user_email = jQuery('#get_user_id_bloxx_client').val();
        var aside_length = $("#leftCategorySidebar_layouts .layout_industries select").length;
        var site_url = jQuery('#site_url').val();
        var getText = $this.html();

        var imageurl=bloxxapi.imageurl;
        var error_image=imageurl+"/images/error-frame.png";

        if(bloxxapi.dropbox=="dropbox_disable" && bloxxapi.kitz_builder=="no"){
            Swal.fire({
                title: "<img src='"+error_image+"'/> Error!",
                text: "Oops! Looks Please connect dropbox for continue",
                confirmButtonColor: '#000'                                
            });
            return false;
        } else if(bloxxapi.dropbox=="dropbox_enable" && bloxxapi.kitz_builder!="no"){
            //Plugin ajax url
            var ajax_url_layouts= bloxx.ajax_url;
            var ajax_data={
                'action': 'kitz_load_ajax_industries',
                'type': "layout",
                "_nonce": bloxx.ajax_nonce,
            };
        } else {
            //Kitzpro URL
            var ajax_url_layouts = bloxxapi.ajax_url_layouts;
            var ajax_url = bloxxapi.ajax_url;
            var website_url=bloxxapi.siteurl;
            var builder_key = bloxxapi.builder_key;
            var api_token = bloxxapi.api_token; 
            var account_image=imageurl+"/images/account-icon.png"; 
            var ajax_data= {
                'action': 'load_ajax_industries',
                'builder_key': builder_key,
                'api_token': api_token,
                'current_user_email': current_user_email,
                "_nonce": bloxx.ajax_nonce,
                'site_url': site_url
            };
        }

        if (aside_length == 0) {
            if (ajax_url == "activate") {
				Swal.fire({
					title: "<img src='"+error_image+"'/> Error!",
					text: "Please activate Divi theme or Kitz Pro Builder plugin to continue",
					confirmButtonColor: '#000'                                
				});
            } else {
                $.ajax({
                    type: "POST",
                    url: ajax_url_layouts,
                    data: ajax_data,
                    beforeSend: function () {
                        $this.html('<i class="fa fa-spinner fa-spin"></i>');
                    },
                    success: function (resp) {
                        if ($.trim(resp) == "Data_not_found") {
                            var error_image=imageurl+"/images/error-frame.png";
                            Swal.fire({
                                title: "<img src='"+error_image+"'/> Error!",
                                text: "Oops! Looks like your current API key isn’t working! Generate a new API key or check your Kitz Pro Builder plan",
                                confirmButtonColor: '#000'                                
                            });
                            $this.html(getText);
                        } else if($.trim(resp) == "Dropbox_not_found"){
                            var error_image=imageurl+"/images/error-frame.png";
                            Swal.fire({
                                title: "<img src='"+error_image+"'/> Error!",
                                text: "Dropbox layout functionality coming soon, Please try again later.",
                                confirmButtonColor: '#000'                                
                            });
                            $this.html(getText);
                        } else {
                            $this.html(getText);
                            //$("#leftCategorySidebar_layouts .builder_categories").html(resp);
                            $("#leftCategorySidebar_layouts .layout_industries").html(resp);

                            // trigger all industries dropdown change event
                            $(".layout_industries #service_type").val('all').trigger('change');
                            // end trigger

                            open_sidebar($this);
                        }
                    },
                    error: function () {
                        $this.html(getText);
                        console.log("Failed to get data through Kitz Pro Builder API");
                    }
                });
            }
        } else {
            open_sidebar($this);
        }
    });


    // on change industries dropdown show layout categories    
    $(document).on("change", ".layout_industries #service_type", function (e) {
        var $this = $(this);
        var current_user_email = jQuery('#get_user_id_bloxx_client').val();
        var aside_length = $("#leftCategorySidebar_layouts .builder_categories li").length;
        var site_url = jQuery('#site_url').val();
        var service_type_id = $this.val();
        if(service_type_id!=''){
            industry_id = service_type_id;
        } else {
            industry_id = '0';
        }

        //var ajax_url_layouts = bloxxapi.ajax_url_layouts;

        if($("#global_radio").prop('checked')) {
            var ajax_url_layouts= bloxxapi.kitz_unauth_layout;
        } else if(bloxxapi.builder_key==1) {
            var ajax_url_layouts= bloxxapi.kitz_unauth_layout;
        } else {
            var ajax_url_layouts= bloxxapi.ajax_url_layouts;
        }


        var getText = $this.html();
        var ajax_url = bloxxapi.ajax_url;
        var imageurl=bloxxapi.imageurl;
        var website_url=bloxxapi.siteurl;
        var builder_key = bloxxapi.builder_key;
        var api_token = bloxxapi.api_token; 
        var account_image=imageurl+"/images/account-icon.png";   
        
        $.ajax({
            type: "POST",
            url: ajax_url_layouts,
            data: {
                'action': 'load_ajax_cats',
                'builder_key': builder_key,
                'api_token': api_token,
                'current_user_email': current_user_email,
                'site_url': site_url,
                "_nonce": bloxx.ajax_nonce,
                'industry_id' : industry_id
            },
            beforeSend: function () {
               // $this.html('<i class="fa fa-spinner fa-spin"></i>');
            },
            success: function (resp) {
                if ($.trim(resp) == "Data_not_found") {
                   // alert('1');
                    var error_image=imageurl+"/images/error-frame.png";
                    Swal.fire({
                        title: "<img src='"+error_image+"'/> Error!",
                        text: "Oops! Looks like your current API key isn’t working! Generate a new API key or check your Kitz Pro Builder plan",
                        confirmButtonColor: '#000'                                
                    });
                   // $this.html(getText);
                } else {
                    $("#leftCategorySidebar_layouts .builder_categories").html(resp);
                }
            },
            error: function () {
               // alert('3');
               // $this.html(getText);
                console.log("Failed to get data through Kitz Pro Builder API");
            }
        });
        
    });


    
    //Set global check box

    $("body").on("click", "#global_radio", function(){
        $("#leftCategorySidebar .builder_categories").empty();
        $("#leftCategorySidebar_layouts .layout_industries select").remove();

        // hide layouts
        $("#leftCategorySidebar_layouts").removeClass('sidebar-in');
        $("#leftCategorySidebar_layouts").removeAttr("style");
        $(".open-sidebar-layouts").removeClass('active');
    });



    //Section Category Load From API Ajax When clicking on openside bar class

    $(document).on("click", ".open-sidebar a", function (e) {
        var $this = $(this);
        var imageurl=bloxxapi.imageurl;
        var error_image=imageurl+"/images/error-frame.png";
        var getText = $this.html();

        if(bloxxapi.dropbox=="dropbox_disable" && bloxxapi.kitz_builder=="no"){
            $this.html(getText);
            Swal.fire({
                title: "<img src='"+error_image+"'/> Error!",
                text: "Oops! Looks Please connect dropbox for continue",
                confirmButtonColor: '#000'                                
            });
            return false;
        } else if(bloxxapi.dropbox=="dropbox_enable" && bloxxapi.kitz_builder=="no"){
            //Plugin ajax url
            var ajax_url= bloxx.ajax_url;
            var ajax_data={
                'action': 'kitz_load_ajax_cats',
                'type': "section",
                "_nonce": bloxx.ajax_nonce
            };
        } else {
            //Kitzpro URL
            var ajax_url= bloxxapi.ajax_url;
            var getText = $this.html();
            //var ajax_url = bloxxapi.ajax_url;
            var website_url=bloxxapi.siteurl;
            var builder_key = bloxxapi.builder_key;
            var api_token = bloxxapi.api_token; 
            var account_image=imageurl+"/images/account-icon.png"; 
            var current_user_email = jQuery('#get_user_id_bloxx_client').val();
            var site_url = jQuery('#site_url').val();
            var ajax_data= {
                'action': 'load_ajax_cats',
                'builder_key': builder_key,
                'api_token': api_token,
                'current_user_email': current_user_email,
                "_nonce": bloxx.ajax_nonce,
                'site_url': site_url
            };

        }

        var aside_length = $("#leftCategorySidebar .builder_categories li").length;

        if (aside_length == 0) {
            if (ajax_url == "activate") {
                $this.html(getText);
				Swal.fire({
					title: "<img src='"+error_image+"'/> Error!",
					text: "Please activate Divi theme or Divi builder plugin to continue",
					confirmButtonColor: '#000'                                
				});
            } else {
                $.ajax({
                    type: "POST",
                    url: ajax_url,
                    data: ajax_data,
                    beforeSend: function () {
                        $this.html('<i class="fa fa-spinner fa-spin"></i>');
                    },
                    success: function (resp) {
                        if ($.trim(resp) == "Data_not_found") {
                            var error_image=imageurl+"/images/error-frame.png";
                            Swal.fire({
                                title: "<img src='"+error_image+"'/> Error!",
                                text: "Oops! Looks like your current API key isn’t working! Generate a new API key or check your Kitz Pro Builder plan",
                                confirmButtonColor: '#000'                                
                            });
                            $this.html(getText);
                        } else if($.trim(resp) == "Data_not_found"){
                            var error_image=imageurl+"/images/error-frame.png";
                            Swal.fire({
                                title: "<img src='"+error_image+"'/> Error!",
                                text: "Dropbox data not found, Please try again later.",
                                confirmButtonColor: '#000'                                
                            });
                            $this.html(getText);
                        } else {
                            $this.html(getText);
                            $("#leftCategorySidebar .builder_categories").html(resp);
                            open_sidebar($this);
                        }
                    },
                    error: function () {
                        $this.html(getText);
                        console.log("Failed to get data through Kitz Pro Builder API");
                    }
                });
            }
        } else {
            open_sidebar($this);
        }
    });

    
    function key_connect(ajax_url, website_url, siteblox_username, siteblox_key, siteblox_status, action){
        $.ajax({
            type: "POST",
            url: ajax_url,
            dataType: "json",
            data: {
                'action': action,
                'website_url': website_url,
                'siteblox_username': siteblox_username,
                'siteblox_key': siteblox_key,
                "_nonce": bloxx.ajax_nonce,
                'siteblox_status': siteblox_status
            },
            beforeSend: function () {
                swal.fire({
                    customClass: {
                        container: 'swal2_spinner',
                    },
                    html: '<div class="builder_spinner" id="loadingSpinner"></div>',
                    showConfirmButton: false,
                    onRender: function () {
                        $('.swal2-content').prepend(sweet_loader);
                    }
                });                
            },
            success: function (resp) {
                if(resp.code==200){
                    Swal.fire({
                        title: "Success!", 
                        text: resp.message,
                        confirmButtonColor: '#000', 
                        icon: "success"
                    });
                    setTimeout(function(){
                        window.location.href="";
                    }, 1500);
                } else {
                    Swal.fire({
                        title: "Error!", 
                        text: resp.message,
                        confirmButtonColor: '#000', 
                        icon: "error"
                    });
                }
                
            }, error:function(){
                Swal.fire({
                    title: "Error!", 
                    text: "Please try again later",
                    confirmButtonColor: '#000', 
                    icon: "error"
                });
            }
        });
    }




    function open_sidebar($this) {
        //console.log($this);
        $(".switch-sidebar.active").trigger("click");

        var i=1;
        $(".left-list li").each(function(){
            if(i!=1){
                var get_old_image= $(this).find("a").attr('data-old-img');
                $(this).find("img").attr("src", get_old_image);
            }
            i++;
        });

        var get_img=$this.attr('data-img');
        $this.find("img").attr("src", get_img);

        if($this.attr('data-type')=='layout'){
            // alert('here');
            
            $('.builder_desktop_sidebar ul li.open-sidebar').removeClass('active');



            $this.parent().addClass("active");

            if($('#leftCategorySidebar_layouts').hasClass('sidebar-in')){
                $("#leftCategorySidebar_layouts").removeClass('sidebar-in');
                $("#leftCategorySidebar_layouts").removeAttr("style");
                $(".layouts_lists .builder_posts").hide();
            }else{
                $("#leftCategorySidebar").removeClass('sidebar-in');
                $("#leftCategorySidebar").removeAttr("style");

                

                $(".switch-sidebar").removeAttr("style");
               // $(this).removeClass("active");
                $(".builder_posts").css({
                    'overflow-y': 'scroll',
                    'left': '360px'
                });
                $(".builder_posts").hide();


                // show layouts
                $("#leftCategorySidebar_layouts").addClass('sidebar-in');
                $("#leftCategorySidebar_layouts").attr("style", "left:60px");
                $this.parent().addClass("active");
            }
                
        }else{
            $(".layouts_lists .builder_posts").hide();
            if ($this.parent().hasClass("active")) {
                $("#leftCategorySidebar").removeClass('sidebar-in');
                $("#leftCategorySidebar").removeAttr("style");
                $(this).removeClass("active");
                $this.parent().removeClass("active");
                $(".builder_posts").css({
                    'overflow-y': 'scroll',
                    'left': '360px'
                });
                $(".builder_posts").hide();

                // hide layouts
                $("#leftCategorySidebar_layouts").removeClass('sidebar-in');
                $("#leftCategorySidebar_layouts").removeAttr("style");
                $(".open-sidebar-layouts").removeClass('active');



            } else {
                $("#leftCategorySidebar").addClass('sidebar-in');
                $("#leftCategorySidebar").attr("style", "left:60px");
                $this.parent().addClass("active");


                // hide layouts
                $("#leftCategorySidebar_layouts").removeClass('sidebar-in');
                $("#leftCategorySidebar_layouts").removeAttr("style");
                $(".open-sidebar-layouts").removeClass('active');
            }
        }
        
    }



    //Section Load From ajax when clicked on category from sidebar
    function load_sections(cats_id, $this) {
    	//alert('load_sections');
        var cat_text = $this.html();
        var imageurl=bloxxapi.imageurl;
        var error_image=imageurl+"/images/error-frame.png";
        

        if(bloxxapi.dropbox=="dropbox_disable" && bloxxapi.kitz_builder=="no") {
            $this.html(cat_text);
            Swal.fire({
                title: "<img src='"+error_image+"'/> Error!",
                text: "Oops! Looks Please connect dropbox for continue",
                confirmButtonColor: '#000'                                
            });
            return false;
        } else if(bloxxapi.dropbox=="dropbox_enable" && bloxxapi.kitz_builder=="no"){
            //Plugin ajax url
            var ajax_url= bloxx.ajax_url;
            var ajax_data={
                "action": "kitz_load_sections",
                'dropbox_folder': cats_id,
                "_nonce": bloxx.ajax_nonce
            };
        } else {
            //Kitzpro URL
            var ajax_url= bloxxapi.ajax_url;
            var page_term = $this.attr('data-id');
            var ajax_url = bloxxapi.ajax_url;
            var builder_key = bloxxapi.builder_key;
            var api_token = bloxxapi.api_token;
            var website_url=bloxxapi.siteurl;
            var current_user_email = jQuery('#get_user_id_bloxx_client').val();
            var site_url = jQuery('#site_url').val();
            var ajax_data= {
                'action': 'section_ajax_load',
                'page_term': page_term,
                'cats_id': cats_id,
                'builder_key': builder_key,
                'api_token': api_token,
                'current_user_email': current_user_email,
                "_nonce": bloxx.ajax_nonce,
                'site_url': site_url
            };
        }

		if (ajax_url == "activate") {
			Swal.fire({
				title: "<img src='"+error_image+"'/> Error!",
				text: "Please activate Divi theme or Kitz Pro Builder plugin to continue",
				confirmButtonColor: '#000'                                
			});
		} else {
            $.ajax({
                type: "POST",
                url: ajax_url,
                data: ajax_data,
                beforeSend: function () {
                    $this.html(cat_text + ' <i class="fa fa-spinner fa-spin"></i>');
                    $(".sections_lists section").removeClass("active_slide");
                    $(".sections_lists .builder_posts").hide();
                },
                success: function (resp) {
                    if ($.trim(resp) == "Data_not_found") {
                        var error_image=imageurl+"/images/error-frame.png";
                        Swal.fire({
                            title: "<img src='"+error_image+"'/> Error!",
                            text: "Oops! sections not found, Please try after some time",
                            confirmButtonColor: '#000'                                
                        });
                        $this.html(cat_text);
                    } else {
                        var count_section = $(".sections_lists").find("section").length;
                        if (count_section == 0) {
                            $(".sections_lists").html(resp);
                        } else {
                            $(".sections_lists section:last-child").after(resp);
                        }                        
                        
                        if (cats_id== 502) {} else if(cats_id == 176){} else {
                            sortable_layout();
                        }

                        

                        //setTimeout(function () {
                            $(".sections_lists .builder_posts").css({
                                'position': 'fixed',
                                'overflow-y': 'scroll',
                                'left': '360px'
                            });


                            // $(".sections_lists .builder_posts").show("slide", {
                            //     direction: "left"
                            // }, 800);

                            $(".sections_lists .builder_posts").show();

                            $this.html(cat_text);
                            loadmore_scroll_clicked();
                        //}, 500);
                    }
                },
                error: function () {
                    $this.html(cat_text);
                }
            });
        }
    }




    jQuery(document).on('change','#service_type',function(){
        var service_type_id = jQuery(this).val();
        //console.log(service_type_id);
        $('#service_type_id').val(service_type_id);
    });

    //LAYOUTS Load From ajax when clicked on category from sidebar
    function load_layouts(cats_id, $this,industry_id) {
        //alert('load_sections');
        var cat_text = $this.html();
        var page_term = $this.attr('data-id');
        var ajax_url = bloxxapi.ajax_url;
        var ajax_url_layouts = bloxxapi.ajax_url_layouts;
        var builder_key = bloxxapi.builder_key;
        var api_token = bloxxapi.api_token;
        var imageurl=bloxxapi.imageurl;
        var website_url=bloxxapi.siteurl;
        var current_user_email = jQuery('#get_user_id_bloxx_client').val();
       // var auth_type = jQuery('#auth_type').val();
        var site_url = jQuery('#site_url').val();

        if (ajax_url == "activate") {
           var error_image=imageurl+"/images/error-frame.png";
            Swal.fire({
                title: "<img src='"+error_image+"'/> Error!",
                text: "Please activate Divi theme or Divi builder plugin to continue",
                confirmButtonColor: '#000'                                
            });
        } else if (ajax_url == "disconnect") {
            Swal.fire({                    
                title: "<img src='"+account_image+"'/>Account Verification",
                text: "Please enter your API key to gain access to theKitz Pro Builder and library.",
                input: 'text',                    
                showCancelButton: false,
                confirmButtonColor: '#9F05C5',
                //cancelButtonColor: '#000',
                confirmButtonText: 'Submit',
                inputValidator: (value) => {
                    if (!value) {
                      return "Enter Your Kitz Pro Builder API Key"
                    }
                }
                }).then((result) => {
                if (result.isConfirmed) {
                    siteblox_key=result.value;
                    var action="kitz_siteblox_key_saved";
                    var ajax_url= bloxx.ajax_url;
                    var siteblox_status="kitz_connect";
                    key_connect(ajax_url, website_url, siteblox_key, siteblox_status, action);
                }
            });
        } else {
            if($("#global_radio").prop('checked')) {
                var ajax_url_layouts = bloxxapi.kitz_unauth_layout;
            } else if(bloxxapi.builder_key==1) {
                var ajax_url_layouts= bloxxapi.kitz_unauth_layout;
            } else {
                var ajax_url_layouts = bloxxapi.ajax_url_layouts;
            }

            $.ajax({
                type: "POST",
                url: ajax_url_layouts,
                data: {
                    'action': 'section_ajax_load',
                    'page_term': page_term,
                    'cats_id': cats_id,
                    'industry_id':industry_id,
                    'builder_key': builder_key,
                    'api_token': api_token,
                    "_nonce": bloxx.ajax_nonce,
                    'current_user_email': current_user_email,
                   // 'auth_type': auth_type,
                    'site_url': site_url,

                },
                beforeSend: function () {
                    $this.html(cat_text + ' <i class="fa fa-spinner fa-spin"></i>');
                    $(".layouts_lists section").removeClass("active_slide");
                    $(".layouts_lists .builder_posts").hide();
                },
                success: function (resp) {
                    if ($.trim(resp) == "No_Layout_Found") {
                        var error_image=imageurl+"/images/error-frame.png";
                        Swal.fire({
                            title: "<img src='"+error_image+"'/> Error!",
                            text: "Oops! Selected layout have no any sections",
                            confirmButtonColor: '#000'                                
                        });
                        $this.html(cat_text);
                    } else {
                        var count_section = $(".layouts_lists").find("section").length;
                        if (count_section == 0) {
                            $(".layouts_lists").html(resp);
                        } else {
                            $(".layouts_lists section:last-child").after(resp);
                        } 
                        if (cats_id== 502) {} else if(cats_id == 176){} else {
                            sortable_layout();
                        }

                        setTimeout(function () {
                            $(".layouts_lists .builder_posts").css({
                                'position': 'fixed',
                                'overflow-y': 'scroll',
                                'left': '360px'
                            });

                            $(".layouts_lists .builder_posts").show("slide", {
                                direction: "left"
                            }, 800);

                            $this.html(cat_text);

                            loadmore_scroll_clicked();
                        }, 500);
                    }

                },
                error: function () {
                    $this.html(cat_text);
                }
            });
        }
    }



    function loadmore_scroll_clicked() {
        $('.sections_lists .builder_posts,.layouts_lists .builder_posts').on('scroll', function () {
            if ($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight) {
                $(this).find(".load_more a").trigger("click");
            }
        })
    }


    $("body").on("click", ".section_more_load", function () {
        var cats_id = $(this).attr('data-id');
        var $this = $(this);
        var btn_text = $this.html();
        loadfrom_ajaxsection(cats_id, $this, btn_text);
    });


    $("body").on("click", ".layout_more_load", function () {
        var cats_id = $(this).attr('data-id');
        var industry_id = $('#service_type_id').val();
        var $this = $(this);
        var btn_text = $this.html();
        loadfrom_ajaxsection_layouts(industry_id,cats_id, $this, btn_text);
    });

    // layouts ajax load more
    function loadfrom_ajaxsection_layouts(industry_id,cats_id, $this, btn_text) {             //doSomething
        var imageurl=bloxxapi.imageurl;
        var error_image=imageurl+"/images/error-frame.png";

        if(bloxxapi.dropbox=="dropbox_disable" && bloxxapi.kitz_builder=="no"){
            Swal.fire({
                title: "<img src='"+error_image+"'/> Error!",
                text: "Oops! Looks Please connect dropbox for continue",
                confirmButtonColor: '#000'                                
            });
        } else if(bloxxapi.dropbox=="dropbox_enable" && bloxxapi.kitz_builder=="no"){
            //Plugin ajax url
            var ajax_url= bloxx.ajax_url;
            var ajax_data={
                'action': 'kitz_load_ajax_cats',
                'type': "section",
                "_nonce": bloxx.ajax_nonce
            };
        } else {
            //Kitzpro URL
            var ajax_url_layouts = bloxxapi.ajax_url_layouts;

            var section_offset = $this.attr('data-offset');
            var total_count = $this.attr('total-counts');
            var ajax_url = bloxxapi.ajax_url;
            var builder_key = bloxxapi.builder_key;
            var api_token = bloxxapi.api_token;
            var imageurl=bloxxapi.imageurl;
            var website_url=bloxxapi.siteurl;
            var current_user_email = jQuery('#get_user_id_bloxx_client').val();
            var site_url = jQuery('#site_url').val();
            var ajax_url_layouts = bloxxapi.ajax_url_layouts;
            var ajax_data= {
                'action': 'layouts_ajax_load_more',
                'ajax_offset': section_offset,
                'cats_id': cats_id,
                'industry_id': industry_id,
                'builder_key': builder_key,
                'api_token': api_token,
                'current_user_email': current_user_email,
                "_nonce": bloxx.ajax_nonce,
                'site_url': site_url
            };
        }



        if (ajax_url == "activate") {
            Swal.fire({
                title: "<img src='"+error_image+"'/> Error!",
                text: "Please activate Divi theme or Divi builder plugin to continue",
                confirmButtonColor: '#000'                                
            });
        }  else {
            $.ajax({
                type: "POST",
                url: ajax_url_layouts,
                data: ajax_data,
                beforeSend: function () {
                    //$("#cat_post_"+cats_id).addClass("slide_reload");
                    $this.prop('disabled', true).css("pointer-events", "none");
                    $this.html('Please Wait <i class="fa fa-spinner fa-spin"></i>');
                },
                success: function (resp) {
                    if ($.trim(resp) == "Data_not_found") {
                        var error_image=imageurl+"/images/error-frame.png";
                        Swal.fire({
                            title: "<img src='"+error_image+"'/> Error!",
                            text: "Oops! Looks like your current API key isn’t working! Generate a new API key or check your Kitz Pro Builder plan",
                            confirmButtonColor: '#000'                                
                        });
                        $this.html(getText);
                    } else {
                       // console.log(resp);
                        //$("#cat_post_" + cats_id + " .builder_inner_dragpost:nth-last-child(2)").html(resp);
                        $(".layouts_lists #cat_post_" + cats_id).html(resp);
                        $(".layouts_lists #cat_post_" + cats_id + " .ajax_loader").remove();

                        console.log("rock: "+cats_id);

                        if (cats_id =="502") {                            
                            console.log("Load footer");
                        } else if(cats_id == "176"){
                            console.log("Load header");
                        } else {                            
                            sortable_layout();
                        }

                        setTimeout(function () {
                            $this.prop('disabled', false).css("pointer-events", "auto");
                            $this.html(btn_text);
                            $(".layouts_lists #cat_post_" + cats_id).removeClass("slide_reload");
                            $this.parent().remove();

                            var offset_count = $(".layouts_lists .builder_posts div.load_more a").attr('data-offset');
                            if (parseInt(total_count) < parseInt(offset_count)) {
                                $(".layouts_lists .builder_posts div.load_more").hide();
                            }
                        }, 1500);

                        loadmore_scroll_clicked();
                    }
                },
                error: function () {
                    $(".layouts_lists #cat_post_" + cats_id).removeClass("slide_reload");
                    $this.prop('disabled', false).css("pointer-events", "auto");
                    $this.html(btn_text);
                    console.log("Failed to load section from server");
                }
            });
            $(".builder_posts").off('scroll');
        }        
    }


    function loadfrom_ajaxsection(cats_id, $this, btn_text) {             //doSomething
    	//alert('loadfrom_ajaxsection');
        var section_offset = $this.attr('data-offset');
        var total_count = $this.attr('total-counts');
        var ajax_url = bloxxapi.ajax_url;
        var builder_key = bloxxapi.builder_key;
        var api_token = bloxxapi.api_token;
        var imageurl=bloxxapi.imageurl;
        var website_url=bloxxapi.siteurl;
        var current_user_email = jQuery('#get_user_id_bloxx_client').val();
       // var auth_type = jQuery('#auth_type').val();
        var site_url = jQuery('#site_url').val();

        if (ajax_url == "activate") {
		   var error_image=imageurl+"/images/error-frame.png";
			Swal.fire({
				title: "<img src='"+error_image+"'/> Error!",
				text: "Please activate Divi theme or Divi builder plugin to continue",
				confirmButtonColor: '#000'                                
			});
		} else if (ajax_url == "disconnect") {
            Swal.fire({                    
                title: "<img src='"+account_image+"'/>Account Verification",
                text: "Please enter your API key to gain access to the Kitz Pro Builder and library.",
                input: 'text',                    
                showCancelButton: false,
                confirmButtonColor: '#9F05C5',
                //cancelButtonColor: '#000',
                confirmButtonText: 'Submit',
                inputValidator: (value) => {
                    if (!value) {
                      return "Enter Your Kitz Pro Builder API Key"
                    }
                }
                }).then((result) => {
                if (result.isConfirmed) {
                    siteblox_key=result.value;
                    var action="kitz_siteblox_key_saved";
                    var ajax_url= bloxx.ajax_url;
                    var siteblox_status="kitz_connect";
                    key_connect(ajax_url, website_url, siteblox_key, siteblox_status, action);
                }
            });
        } else {
            if($("#global_radio").prop('checked')) {
                var ajax_url = bloxxapi.kitz_unauth_section;
            } else if(bloxxapi.builder_key==1) {
                var ajax_url= bloxxapi.kitz_unauth_section;
            }  else {
                var ajax_url = bloxxapi.ajax_url;
            }


            $.ajax({
                type: "POST",
                url: ajax_url,
                data: {
                    'action': 'ajax_load_more',
                    'ajax_offset': section_offset,
                    'cats_id': cats_id,
                    'builder_key': builder_key,
                    'api_token': api_token,
                    "_nonce": bloxx.ajax_nonce,
                    'current_user_email': current_user_email,
                    //'auth_type': auth_type,
                    'site_url': site_url
                },
                beforeSend: function () {
                    //$("#cat_post_"+cats_id).addClass("slide_reload");
                    $this.prop('disabled', true).css("pointer-events", "none");
                    $this.html('Please Wait <i class="fa fa-spinner fa-spin"></i>');
                },
                success: function (resp) {
                    if ($.trim(resp) == "Data_not_found") {
                        var error_image=imageurl+"/images/error-frame.png";
                        Swal.fire({
                            title: "<img src='"+error_image+"'/> Error!",
                            text: "Oops! Looks like your current API key isn’t working! Generate a new API key or check your Kitz Pro Builder plan",
                            confirmButtonColor: '#000'                                
                        });
                        $this.html(getText);
                    } else {
                        //$("#cat_post_" + cats_id + " .builder_inner_dragpost:nth-last-child(2)").html(resp);
                        $("#cat_post_" + cats_id).html(resp);
                        $("#cat_post_" + cats_id + " .ajax_loader").remove();

                        //console.log("rock: "+cats_id);

                        if (cats_id =="502") {                            
                            console.log("Load footer");
                        } else if(cats_id == "176"){
                            console.log("Load header");
                        } else {                            
                            sortable_layout();
                        }

                        setTimeout(function () {
                            $this.prop('disabled', false).css("pointer-events", "auto");
                            $this.html(btn_text);
                            $("#cat_post_" + cats_id).removeClass("slide_reload");
                            $this.parent().remove();

                            var offset_count = $(".builder_posts div.load_more a").attr('data-offset');
                            if (parseInt(total_count) < parseInt(offset_count)) {
                                $(".builder_posts div.load_more").hide();
                            }
                        }, 1500);

                        loadmore_scroll_clicked();
                    }
                },
                error: function () {
                    $("#cat_post_" + cats_id).removeClass("slide_reload");
                    $this.prop('disabled', false).css("pointer-events", "auto");
                    $this.html(btn_text);
                    console.log("Failed to load section from server");
                }
            });
            $(".builder_posts").off('scroll');
        }        
    }



    //sortable_layout();
    function sortable_layout() {

        var clone, before, parent
        $('.connectedSortable').each(function () {
            $(this).sortable({
                placeholder: 'sort-highlight',
                forcePlaceholderSize: true,
                connectWith: '.connectedSortable',                
                helper: "clone",
                zIndex: 999999,
                start: function (event, ui) {
                    $(".dropable_area").hide();
                    if ($(window).width() < 760) {
                        $(".builder_posts").hide();
                        //$(".mob-open-sidebar").removeClass("active");
                        $(".mob-switch-sidebar").removeClass("active");
                        $("#mobCategoryTabs").hide();
                        $("#mobProjectTabs").hide();

                    } else {
                        $(".open-sidebar").removeClass("active");
                        $(".switch-sidebar").removeClass("active");
                        $("#leftCategorySidebar").removeAttr("style");
                        $("#leftCategorySidebar").removeClass('sidebar-in');
                        $("#left_project").removeAttr("style");
                        $(".builder_posts").css({
                            'left': '60px',
                            'overflow-y': 'inherit'
                        }).animate({
                            "display": "none"
                        }, "slow");

                    }

                    //$(".builder_posts").hide("slide", {direction: "left"}, 1000);
                    $(ui.item).show();
                    clone = $(ui.item).clone();
                    before = $(ui.item).prev();
                    parent = $(ui.item).parent();
                },
                change: function (event, ui) {
                    var get_drop_lenth = $(".builder_inner_dropable > div.card").length;
                    if (get_drop_lenth == 0) {
                        $(".dropable_area").show();
                    } else {
                        $(".dropable_area").hide();
                    }
                },
                receive: function (event, ui) { //only when dropped from one to another!

                    /*var drop_post_id = ui.item[0].childNodes[1].id;
                     var drop_position= ui.item.index();*/
                    $(".dropable_area").hide();
                    $(".builder_inner_dropable .card .action_btns").show();
                    if ($(window).width() > 600) {
                        $(".mob-open-sidebar").removeClass("active");
                        $(".mob-switch-sidebar").removeClass("active");
                        $("#mobCategoryTabs").hide();
                        $("#mobProjectTabs").hide();
                        $(".builder_posts").css({
                            'overflow-y': 'scroll',
                            'left': '360px'
                        });
                        $(".builder_posts").hide();
                    } else {
                        $(".builder_posts").css({
                            'overflow-y': 'scroll',
                            'left': '360px'
                        });
                        $(".builder_posts").hide();

                    }
                    if (before.length)
                        before.after(clone);
                    else
                        parent.prepend(clone);

                    $(".builder_layout_save").addClass("page_draft");


                    var get_drop_lenth = $(".builder_inner_dropable > div.card").length;
                    if (get_drop_lenth == 1) {
                        $(".builder_downarrow").hide();
                        $(".builder_uparrow").hide();
                    } else {
                        $(".builder_downarrow").show();
                        $(".builder_uparrow").show();
                    }
                }
            }).disableSelection();
        });
    }






    $(document).on("click", "a.builder_remove_layout", function (e) {
        //alert(1);
        var $this = $(this);
        Swal.fire({
            title: 'Are you sure?',
            text: "You want to delete this",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#000',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $(".builder_layout_save").addClass("page_draft");
                $($this).parent().parent().remove();
                $(".fixed-btn-save").show();
                //get count of item when deleted
                /*var changed_array=[];
                 $(".builder_inner_dropable .card > .builder-dragpost").each(function(){
                 var get_content=$(this).attr('id')
                 changed_array.push(get_content);
                 });                                 
                 $("#section_count_default").val(changed_array);*/
                //End get count of item when deleted

                var get_drop_lenth = $(".builder_inner_dropable > div.card").length;
                if (get_drop_lenth == 0) {
                    $(".builder_inner_dropable").html('<div class="dropable_area ui-sortable-handle"><h1><span><i class="fas fa-expand-arrows-alt"></i></span>Drag & Drop Sections</h1></div>');
                } else if (get_drop_lenth == 1) {
                    $(".builder_downarrow").hide();
                    $(".builder_uparrow").hide();
                } else {
                    $(".builder_downarrow").show();
                    $(".builder_uparrow").show();
                }
            }
        });
        e.preventDefault();
    });



    $("body").on("click", ".builder_uparrow", function () {
        var btn = $(this);
        $(".builder_layout_save").addClass("page_draft");
        moveUp(btn.parents('.card'));
    });

    $("body").on("click", ".builder_downarrow", function () {
        var btn = $(this);
        $(".builder_layout_save").addClass("page_draft");
        moveDown(btn.parents('.card'));

    });

    function moveUp(item) {
        var prev = item.prev();
        if (prev.length == 0)
            return;
        prev.css('z-index', 999).css('position', 'relative').animate({top: item.height()}, 250);
        item.css('z-index', 1000).css('position', 'relative').animate({top: '-' + prev.height()}, 300, function () {
            prev.css('z-index', '').css('top', '').css('position', '');
            item.css('z-index', '').css('top', '').css('position', '');
            item.insertBefore(prev);
        });
    }
    function moveDown(item) {
        var next = item.next();
        if (next.length == 0)
            return;
        next.css('z-index', 999).css('position', 'relative').animate({top: '-' + item.height()}, 250);
        item.css('z-index', 1000).css('position', 'relative').animate({top: next.height()}, 300, function () {
            next.css('z-index', '').css('top', '').css('position', '');
            item.css('z-index', '').css('top', '').css('position', '');
            item.insertAfter(next);
        });
    }




    $("li.builder_layout_save a,builder_layout_save a").click(function () {
        var $this = $(this);
        var get_html = $this.html();
        var project_id = $this.attr('data-id');
        var action_to_perform = $this.attr('data-id');


        var send_content = [];
        $(".builder_inner_dropable .card .builder_layout").each(function () {
            var get_content = $(this).val();
            send_content.push(get_content);
        });

        var ajax_url = bloxx.ajax_url;
        $.ajax({
            type: "POST",
            dataType: "json",
            url: ajax_url,
            data: {
                action: 'kitz_saveproject',
                "_nonce": bloxx.ajax_nonce,
                builder_prj_id: project_id,
                json_content: send_content,
                action_to_perform: action_to_perform
            },
            beforeSend: function () {
                $this.html('<i class="fa fa-spinner fa-spin"></i>');
            },
            success: function (resp) {                
                if (resp.code == 200) {
                    $("#project_id").val(resp.project_id);
                    $(".project_detail_nm").show();
                    $("ul.left-list .open-sidebar").removeClass("slide_reload");
                    $(".builder_layout_save").removeClass("page_draft");
                    $this.html(get_html);
                    //load_css(project_id, $this, get_html);
                    Swal.fire({
                        title: "Page updated successfully!",
                        confirmButtonColor: '#000',
                        icon: "success"
                    });
                    window.et_pb_init_modules();
                    window.location.href="";
                } else {
                    $this.html(get_html);
                    Swal.fire({
                        title: "Error!",
                        text: resp.message,
                        confirmButtonColor: '#000',
                        icon: "error"
                    });
                }
            },
            error: function () {
                $this.html(get_html);
                Swal.fire({
                    title: "Error!",
                    text: "Please try again later",
                    confirmButtonColor: '#000',
                    icon: "error"
                });
            }
        });
    });




    function load_css(project_id, $this, get_html) {
        //Load Css

        var ajax_url = bloxx.ajax_url;
        $.ajax({
            type: "POST",
            url: ajax_url,
            dataType: "json",
            data: {
                'action': 'kitz_et_builder_load_css',
                "_nonce": bloxx.ajax_nonce,
                'page_id': project_id
            },
            beforeSend: function () {
                $("ul.left-list .open-sidebar").addClass("slide_reload");
            },
            success: function (resp) {
                console.log(resp);
                if (resp.code == 200) {
                    $("ul.left-list .open-sidebar").removeClass("slide_reload");
                    $(".builder_layout_save").removeClass("page_draft");

                    setTimeout(function () {
                        $this.html(get_html);
                        Swal.fire({
                            title: "Page updated successfully!",
                            //text: "You can now see the live code and edit it with Kitz Pro Builder",
                            confirmButtonColor: '#000',
                            icon: "success"
                        });
                        window.et_pb_init_modules();
                        //$("li.builder_layout_save a").html("<img src='/wp-content/plugins/bloxx/images/floppy-icon.png' alt='Save'> Save");
                        window.location.href="";
                    }, 1000);
                }
            }, error: function () {
                $this.html(get_html);
                $("ul.left-list .open-sidebar").removeClass("slide_reload");
                $(".builder_layout_save").removeClass("page_draft");
                $(".builder_inner_dropable .card a.builder_remove_layout").show();
                swal.close();
                console.log("Failed");
            }

        });
    }



    $("body").on("click", ".neo_save_layout a", function(){
        var $this=$(this);
        var get_html = $this.html();
        var project_id = $this.attr('data-id');
        var send_content = [];
        $(".builder_inner_dropable .card .builder_layout").each(function () {
            var get_content = $(this).val();
            send_content.push(get_content);
        });
        var neo_type= "layout";
        //$this.html('<i class="fa fa-spinner fa-spin"></i>');

        (async () => {
            /* inputOptions can be an object or Promise */
            const inputOptions = new Promise((resolve) => {
                setTimeout(() => {
                    resolve({
                        'layout_option': 'Divikitz',
                        'drop_option': 'DropBox'
                    })
                }, 1000)
            })

            const { value: upload_type } = await Swal.fire({
                title: 'Select Save Type',
                input: 'radio',
                inputOptions: inputOptions,
                confirmButtonText: 'Continue',
                showCloseButton: true,
                confirmButtonColor: '#000',           
                customClass: {
                    container:"regenerate_container"
                },
                inputValidator: (value) => {
                    if (!value) {
                        return 'You need to choose something!'
                    }
                }
            });



            if (upload_type=="layout_option") {
                swal.fire({
                    customClass: {
                        container: 'swal2_spinner',
                    },
                    html: '<div class="builder_spinner" id="loadingSpinner"></div>',
                    showConfirmButton: false,
                    onRender: function () {
                        $('.swal2-content').prepend(sweet_loader);
                    }
                });
                get_cat_industry(neo_type, $this, get_html, project_id, send_content, $this);
            } else if(upload_type=="drop_option") {
                swal.fire({
                    customClass: {
                        container: 'swal2_spinner',
                    },
                    html: '<div class="builder_spinner" id="loadingSpinner"></div>',
                    showConfirmButton: false,
                    onRender: function () {
                        $('.swal2-content').prepend(sweet_loader);
                    }
                });
                save_dropbox($this, get_html, project_id, send_content, "layout");
            }
        })()
        //neo_directory(project_id, send_content, neo_type, $this);
    });



    function save_dropbox($this, get_html, project_id, send_content, type){
        var ajax_url = bloxx.ajax_url;
        
        $.ajax({
            type: "POST",
            dataType: "json",
            url: ajax_url,
            data: {
                action: 'savedropbox',
                builder_prj_id: project_id,
                save_type: type,
                "_nonce": bloxx.ajax_nonce,
                json_content: send_content
            },
            beforeSend: function () {
                $this.html('<i class="fa fa-spinner fa-spin"></i>');
            },
            success: function (resp) {
                $this.html(get_html);
                if (resp.code == 202) {
                    Swal.fire({
                        title: "Error!",
                        text: resp.message,
                        confirmButtonColor: '#000',
                        icon: "error"
                    });
                } else {
                    Swal.fire({
                        title: "Success!",
                        text: "Your Layout has been saved into dropbox",
                        confirmButtonColor: '#000',
                        icon: "success"
                    });
                }
            },
            error: function () {
                $this.html(get_html);
                Swal.fire({
                    title: "Error!",
                    text: "Please try again later",
                    confirmButtonColor: '#000',
                    icon: "error"
                });
            }
        });
    }


    function get_cat_industry(neo_type, $this, get_html, project_id, send_content, $this){
        var ajax_url = bloxx.ajax_url;
        $.ajax({
            type: "POST",
            dataType: "json",
            url: ajax_url,
            data: {
                action: 'kitz_cat_industry',
                "_nonce": bloxx.ajax_nonce,
                'neo_type': neo_type
            },
            beforeSend: function () {
                $this.html('<i class="fa fa-spinner fa-spin"></i>');
            },
            success: function (resp) {
                $this.html(get_html);
                if (resp.code == 200) {
                    Swal.fire({
                       title: 'Select Category and Industry',
                       customClass: {
                            container: 'swal2_layout_container',
                        },
                       html: '<label class="form-label select-label">Category: </label><select class="select" id="myCatElement"></select><br/><label class="form-label select-label">Industry: </label><select class="select" id="myindustryElement"></select>',
                        didOpen: () => {
                            $.each(resp.layout_cats, function(cat_id, cat_text) {   
                                $('#myCatElement').append($("<option></option>").attr("value", cat_id).text(cat_text)); 
                            });

                            $.each(resp.layout_industry, function(ind_id, ind_text) {   
                                $('#myindustryElement').append($("<option></option>").attr("value", ind_id).text(ind_text)); 
                            });
                        },
                        showCancelButton: true,
                        confirmButtonColor: '#000',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, save it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            var cat_element= $('#myCatElement').val();
                            var industry_element= $('#myindustryElement').val();
                            neo_directory(project_id, send_content, neo_type, $this, cat_element, industry_element, get_html);
                        }
                    });
                } else {
                    Swal.fire({
                        title: "Error!",
                        text: "Please try again later",
                        confirmButtonColor: '#000',
                        icon: "error"
                    });
                }
            },
            error: function () {
                $this.html(get_html);
                Swal.fire({
                    title: "Error!",
                    text: "Please try again later",
                    confirmButtonColor: '#000',
                    icon: "error"
                });
            }
        });
    }


    function neo_directory(project_id, send_content, neo_type, $this, cat_element, industry_element, get_html){
        var ajax_url = bloxx.ajax_url;
        $.ajax({
            type: "POST",
            dataType: "json",
            url: ajax_url,
            data: {
                action: 'neo_assets',
                builder_prj_id: project_id,
                json_content: send_content,
                catID: cat_element,
                indID: industry_element,
                "_nonce": bloxx.ajax_nonce,
                neo_type:neo_type
            },
            beforeSend: function () {
                $this.html('<i class="fa fa-spinner fa-spin"></i>');
            },
            success: function (resp) {
                $this.html(get_html);
                Swal.fire({
                    title: "Success!",
                    text: "Your Layout has been saved",
                    confirmButtonColor: '#000',
                    icon: "success"
                });
            },
            error: function () {
                $this.html(get_html);
                Swal.fire({
                    title: "Error!",
                    text: "Please try again later",
                    confirmButtonColor: '#000',
                    icon: "error"
                });
            }
        });
    }



//    $('.clickDrop').click(function () {
//        if ($(this).next('.dropdownShow').css("left") == "0px") {
//            $(this).next('.dropdownShow').css({"left": "100%", "opacity": "1"});
//        } else {
//            $(this).next('.dropdownShow').css({"left": "0", "opacity": "0"});
//        }
//    });

    $("body").on("click", ".save_section", function(){
        var $this= $(this);
        var arr_id= $this.attr('id');
        var page_id= $this.attr('data-id');
        var pgID= $this.attr('data-pgid');
        
        var get_text= $this.html();
        var get_content=$(".builder_"+pgID+" .builder_inner_area input").val();
        var imageurl=bloxxapi.imageurl;
        var account_image=imageurl+"/images/account-icon.png"; 
        Swal.fire({                    
            title: "<img src='"+account_image+"'/>Section Name",
            text: "Please enter your section name",
            input: 'text',                    
            showCancelButton: true,
            confirmButtonColor: '#9F05C5',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Submit',
            inputValidator: (value) => {
                if (!value) {
                  return "Please enter section name"
                }
            }
            }).then((result) => {
            if (result.isConfirmed) {
                var section_nm=result.value;

                (async () => {
                    /* inputOptions can be an object or Promise */
                    const inputOptions = new Promise((resolve) => {
                        setTimeout(() => {
                            resolve({
                                'section_option': 'Divikitz',
                                'drop_option': 'DropBox'
                            })
                        }, 1000)
                    })

                    const { value: upload_type } = await Swal.fire({
                        title: 'Select Save Type',
                        input: 'radio',
                        inputOptions: inputOptions,
                        confirmButtonText: 'Continue',
                        showCloseButton: true,
                        confirmButtonColor: '#000',           
                        customClass: {
                            container:"regenerate_container"
                        },
                        inputValidator: (value) => {
                            if (!value) {
                                return 'You need to choose something!'
                            }
                        }
                    });



                    if (upload_type=="section_option") {
                        swal.fire({
                            customClass: {
                                container: 'swal2_spinner',
                            },
                            html: '<div class="builder_spinner" id="loadingSpinner"></div>',
                            showConfirmButton: false,
                            onRender: function () {
                                $('.swal2-content').prepend(sweet_loader);
                            }
                        });
                        divikitz_account_section_save(page_id, arr_id, section_nm, get_content, $this);
                    } else if(upload_type=="drop_option") {
                        swal.fire({
                            customClass: {
                                container: 'swal2_spinner',
                            },
                            html: '<div class="builder_spinner" id="loadingSpinner"></div>',
                            showConfirmButton: false,
                            onRender: function () {
                                $('.swal2-content').prepend(sweet_loader);
                            }
                        });
                        save_dropbox($this, get_text, section_nm, get_content, "section");
                    }
                })()
            }
        });
    });


    function divikitz_account_section_save(page_id, arr_id, section_nm, get_content, $this){
        var ajax_url = bloxx.ajax_url;
        $.ajax({
            type: "POST",
            dataType: "json",
            url: ajax_url,
            data: {
                action: 'neo_assets',                        
                pageID: page_id,
                arrId: arr_id,
                neo_type: "sections",
                section_title: section_nm,
                "_nonce": bloxx.ajax_nonce,
                json_content: get_content
            },
            beforeSend: function () {
                $this.html('<i class="fas fa-sync fa-spin"></i>');
            },
            success: function (resp) {
                $this.html(get_text);
                if (resp.code == 202) {
                    Swal.fire({
                        title: "Error!",
                        text: resp.message,
                        confirmButtonColor: '#000',
                        icon: "error"
                    });
                } else {
                    Swal.fire({
                        title: "Success!",
                        text: resp.message,
                        confirmButtonColor: '#000',
                        icon: "success"
                    });
                }
            },
            error: function () {
                $this.html(get_text);
                Swal.fire({
                    title: "Error!",
                    text: "Please try again later",
                    confirmButtonColor: '#000',
                    icon: "error"
                });
            }
        });
    }


    $("body").on("click", ".move_2divi", function (event) {
        event.preventDefault();
        var $this = $(this);
        var href_link = $this.attr('data-href');
        //var checked = $(this).is(":checked");
        //if (checked) {
        Swal.fire({
            //title: "Neo's Buildr",
            title: '<img src="/wp-content/plugins/neo-buildr/images/Frame.png" alt="..."> Divi Builder',
            text: "You are now entering the standard Divi Builder.",
            // icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#000',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Okay'
        }).then((result) => {
            if (result.isConfirmed) {
                $this.html('<i class="fa fa-spinner fa-spin"></i>');
                var page_href = $this.attr('href');
                var page_id = $this.attr('id');
                var meta_type = "disable";
                update_bloxx_metas(page_id, meta_type, $this)
            } else {
                $this.prop('checked', false);
            }
        });
        //} else {
        //$this.prop('checked', false);
        //}
    });




    //Assign Header and Footer
    $('body').on('click touch', '.assign_headfooter', function () {
        var $this = $(this);
        var type_json = $this.find('input').val();
        var assign_type = $this.attr('data-title');
        var get_img = $this.find("img").attr("src");
        var page_id = $(".builder_layout_save a").attr('data-id');
        var sweet_loader = '<div class="sweet_loader"><svg viewBox="0 0 140 140" width="140" height="140"><g class="outline"><path d="m 70 28 a 1 1 0 0 0 0 84 a 1 1 0 0 0 0 -84" stroke="rgba(0,0,0,0.1)" stroke-width="4" fill="none" stroke-linecap="round" stroke-linejoin="round"></path></g><g class="circle"><path d="m 70 28 a 1 1 0 0 0 0 84 a 1 1 0 0 0 0 -84" stroke="#71BBFF" stroke-width="4" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-dashoffset="200" stroke-dasharray="300"></path></g></svg></div>';
        var ajax_url = bloxx.ajax_url;
        $.ajax({
            type: "POST",
            dataType: "json",
            url: ajax_url,
            data: {
                action: 'kitz_headfooter_assign',
                assign_type: assign_type,
                server_page_id: page_id,
                "_nonce": bloxx.ajax_nonce,
                page_content: type_json,
            },
            beforeSend: function () {
                swal.fire({
                    customClass: {
                        container: 'swal2_spinner',
                    },
                    html: '<div class="builder_spinner" id="loadingSpinner"></div>',
                    showConfirmButton: false,
                    onRender: function () {
                        $('.swal2-content').prepend(sweet_loader);
                    }
                });
            },
            success: function (resp) {
                if (resp.code == 200) {
                    $("#syncBox_" + assign_type).hide();
                    if (assign_type == "assign_header") {
                        if($("header").hasClass('et-l et-l--header')) {
                            $("#page-container header").html("<div class='header_resp'><img src='" + get_img + "'/></div>");
                            $(".bloxx_et_builder header").hide();
                        } else {
                            $(".bloxx_et_builder header").show();
                            $(".bloxx_et_builder header .header_resp").html("<img src='" + get_img + "'/>");
                        }
                    } else {
                        if($("footer").hasClass('et-l et-l--footer')) {
                            $("#page-container footer").html("<div class='footer_resp'><img src='" + get_img + "'/></div>");
                            $(".bloxx_et_builder footer").hide();
                        } else {
                            $(".bloxx_et_builder footer").show();
                            $(".bloxx_et_builder footer .footer_resp").html("<img src='" + get_img + "'/>");
                        }                        
                    }

                    
                    
                    $(".left-list li.open-sidebar a").trigger("click");
                    $(".left-list li.open-sidebar").removeClass("active");
                    //window.location.href="";
                    $(".builder_layout_save a").trigger("click");
                } else {
                    $("#syncBox_" + assign_type).hide();
                    Swal.fire({
                        title: "Error!",
                        text: resp.message,
                        confirmButtonColor: '#000',
                        icon: "error"
                    });
                }
            },
            error: function () {
                $("#syncBox_" + assign_type).hide();
                Swal.fire({
                    title: "Error!",
                    text: "Please try again later",
                    confirmButtonColor: '#000',
                    icon: "error"
                });
            }
        });

    });






    
    $("body").on("click", ".user_action .add_page_restriction", function (e) {
        var $this=$(this);        
        e.preventDefault();
        $(".left-list li.switch-sidebar a").trigger("click");
        Swal.fire({
            title: '<img src="'+bloxxapi.imageurl+'/images/Frame.png" alt="..."> Add New Page',
            input: 'text',
            showCloseButton: true,
            customClass: {
                validationMessage: 'my-validation-message'
            },
            allowOutsideClick: false,
            confirmButtonText: "Start Building",
            confirmButtonColor: "#000",
            customClass: 'swal-wide',
            showDenyButton: false,
            denyButtonText: `Close`,
            preConfirm: (value) => {
                if (!value) {
                    Swal.showValidationMessage(
                            '<i class="fa fa-info-circle"></i> This field is required'
                    )
                }
            }
        }).then(function (project) {
            if (project.isConfirmed) {
                $(".swal-wide button.swal2-confirm").html('Please wait <i class="fa fa-spinner fa-spin" style="font-size:20px"></i>');
                var ajax_url = bloxx.ajax_url;
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: ajax_url,
                    data: {
                        action: 'kitz_createpage', 
                        "_nonce": bloxx.ajax_nonce,                       
                        pnm: project.value                        
                    },
                    beforeSend: function () {
                        //$this.html('<i class="fas fa-sync fa-spin"></i>');
                        swal.fire({
                            customClass: {
                                container: 'swal2_spinner',
                            },
                            html: '<div class="builder_spinner" id="loadingSpinner"></div>',
                            showConfirmButton: false,
                            onRender: function () {
                                $('.swal2-content').prepend(sweet_loader);
                            }
                        });
                    },
                    success: function (resp) {
                        if (resp.code == 202) {
                            Swal.fire({
                                title: "Error!",
                                text: resp.message,
                                confirmButtonColor: '#000',
                                icon: "error"
                            });
                        } else {
                            Swal.fire({
                                title: "Success!",
                                text: resp.message,
                                confirmButtonColor: '#000',
                                icon: "success"
                            });
                            window.location.href=resp.page_link;
                        }
                    },
                    error: function () {
                        Swal.fire({
                            title: "Error!",
                            text: "Please try again later",
                            confirmButtonColor: '#000',
                            icon: "error"
                        });
                    }
                });
            }
        });
        
    });
	
	$("body").on("click", ".layouts_lists .builder_posts .neo_hover", function(){
		var data_image= $(this).attr('data_image');
		var layoutimg_url= "<img src='"+data_image+"'/>";
		$("#layoutModal .modal-content .modal-body").html(layoutimg_url);
		$("#layoutModal").show();
	});


    $("body").on("click", ".modal_close", function(){
        $("#layoutModal").hide();
    });

    window.onclick = function(event) {
      if (event.target == $("#layoutModal")[0]) {
        $("#layoutModal").hide();
      }
    }

});