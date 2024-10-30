jQuery(function($){

    $("#siteblox_connectivity").submit(function(event){
        event.preventDefault();
        var get_ajax_url = bloxxbuilder_admin.ajax_url;
       // console.log(bloxxbuilder_admin);
        var button_action= $("#siteblox_status").val();
        var button_text= $("#siteblox_connectivity #save_connectivity").html();
        $("#siteblox_connectivity #save_connectivity").html('Please Wait <i class="fa fa-spinner fa-spin" style="font-size:20px"></i>');
        $.ajax({
            type : "POST",
            dataType : "json",
            url : get_ajax_url,
            data : $('#siteblox_connectivity').serialize(),
            success: function(resp) {
                $("#siteblox_connectivity #save_connectivity").html(button_text);
                if(resp.code==200){                       
                    Swal.fire({
                        title: "Success!", 
                        text: resp.message,
                        confirmButtonColor: '#000', 
                        icon: "success"
                    });

                    if(button_action=="kitz_connect"){
                        $("#siteblox_status").val('kitz_disconnect');
                        $("#siteblox_connectivity #save_connectivity").removeClass('button-pro').addClass('button-danger');
                        $("#siteblox_connectivity #save_connectivity").html('Disconnect');
                    } else {
                        $("#siteblox_status").val('kitz_connect');
                        $("#siteblox_key").val('');
                        $("#siteblox_connectivity #save_connectivity").removeClass('button-danger').addClass('button-pro');
                        $("#siteblox_connectivity #save_connectivity").html('Connect');
                    }
                } else {
                    Swal.fire({
                        title: "Error!", 
                        text: resp.message,
                        confirmButtonColor: '#000', 
                        icon: "error"
                    });                    
                }
                window.location.href="";
            }, error:function(){
               $("#siteblox_connectivity #save_connectivity").html(button_text);
               Swal.fire({
                    title: "Error!", 
                    text: "Please try again later",
                    confirmButtonColor: '#000', 
                    icon: "error"
                });
            }
        });
    });


    $("body").on("click", ".kitz_dropbox_directory", function(event){
        event.preventDefault();
        var $this= $(this);
        var get_text= $this.html();
        var kitz_type=$("#kitz_directory").val();
        Swal.fire({                    
            title: "Sub Directory",
            text: "Please enter your sub-directory name",
            input: 'text',                    
            showCancelButton: true,
            confirmButtonColor: '#9F05C5',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Submit',
            inputValidator: (value) => {
                if (!value) {
                  return "Please enter sub-directory name"
                }
            }
            }).then((result) => {
            if (result.isConfirmed) {
                var section_nm=result.value;
                var ajax_url = bloxx.ajax_url;
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: ajax_url,
                    data: {
                        "action": 'kitz_dropbox_create_folder',      //include/Bloxx_core.php
                        "_nonce": bloxx.ajax_nonce,
                        "kitz_parent_type": kitz_type,
                        "section_title": section_nm
                    },
                    beforeSend: function () {
                        $this.html('<i class="fas fa-sync fa-spin"></i>');
                    },
                    success: function (resp) {
                        $this.html(get_text);
                        //console.log(resp);
                        if (resp.code == 202) {
                            Swal.fire({
                                title: "Error!",
                                text: resp.message,
                                confirmButtonColor: '#000',
                                icon: "error"
                            });
                        } else {
                            var select = $('#kitz_subdirectory').empty();
                            console.log(resp.subdirectory);
                            
                            $.each(resp.subdirectory, function(i, item) {
                                console.log(item);
                                select.append( '<option value="'+ item.subfolder+ '">'+ item.subfolder+ '</option>' ); 
                            });
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
        });
    });




     $("#dropbox_import").submit(function(event){
        event.preventDefault();
        var kitz_file= $("#json_files");
        if(kitz_file.val()==''){                        
            Swal.fire({
                title: "Error!", 
                text: "Please upload json file",
                confirmButtonColor: '#000', 
                icon: "error"
            });
        } else {
            var extension = kitz_file.val().split('.').pop().toLowerCase();
            var validFileExtensions = ['json'];
            if ($.inArray(extension, validFileExtensions) == -1) {
                Swal.fire({
                    title: "Error!", 
                    text: "Please upload only json format",
                    confirmButtonColor: '#000', 
                    icon: "error"
                });                
            } else {
                dropbox_importjson();
            }
        }
    });

    function dropbox_importjson(){
        var formData = new FormData($("#dropbox_import")[0]);
        var button_text= $("#dropbox_import .kitz_btn").html();
        $("#dropbox_import .kitz_btn").html('Please Wait <i class="fa fa-spinner fa-spin" style="font-size:20px"></i>');
        var get_ajax_url = bloxxbuilder_admin.ajax_url;
        $.ajax({
            type : "POST",
            dataType : "json",
            url : get_ajax_url,
            data : formData,
            contentType: false,
            processData: false,
            success: function(resp) {
                $("#dropbox_import .kitz_btn").html(button_text);
                if(resp.code==200){                       
                    Swal.fire({
                        title: "Success!", 
                        text: resp.message,
                        confirmButtonColor: '#000', 
                        icon: "success"
                    });

                    $('#dropbox_import')[0].reset();
                } else {
                    Swal.fire({
                        title: "Error!", 
                        text: resp.message,
                        confirmButtonColor: '#000', 
                        icon: "error"
                    });                    
                }
            }, error:function(){
               $("#dropbox_import .kitz_btn").html(button_text);
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