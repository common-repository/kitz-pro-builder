<?php
/*
 * Template Name: Bloxx Builder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$page_id = get_queried_object_id();

add_action('admin_head', 'kitz_custom_fonts');

function kitz_custom_fonts() {
  echo '<style>
  #wpadminbar {
      display: none;
    } 
  </style>';
}
?>

<style>
header {
    top: 56px;
    position: relative;
    z-index: 99;
    margin-left: 60px;
}

#main-header, #main-footer {
    display: none;
}

.bloxx_et_builder #main-header {
    position: relative;
    z-index: 9;
}
.bloxx_et_builder #main-header .container {
    width: 87%;
}
.bloxx_et_builder #main-footer .container {
    width: 87%;
}
</style>


<?php if (is_plugin_active('divi-builder/divi-builder.php')) { ?>
    <style>
        #category-page .topWrapmenu, #diviBuilder .topWrapmenu {
            padding: 0;
            left: 0;
            width: 100%;
            right: 0;
            margin: 0;
        }
        .left-list>li>a img {
            width: 100%;
            height: auto;
        }
        .topWrapmenu .builder_bredcumbs {
            padding: 0 0 0 60px !important;
        }
        .topWrapmenu ul.topMenuUser {
            padding-right: 1rem !important;
        }
        #header, #footer {
            display: none;
        }
        #category-page .wrapContent {
            margin-left: 0px !important;
            padding: 0 0rem 3rem 60px;
        }
        .builder_create_template {
            padding-left: 0rem;
        }
        .builder_create_template .builder_inner_dropable {
            width: calc(100% - 50px) !important;
        }

        #left-area .builder_categories li a {
            font-size: 14px;
            font-weight: 400;
        }
        .left-list>li>a {
            height: auto;
            padding: 15px;
        }
        .section_more_load {
            font-size: 14px;
        }
    </style>
<?php } ?>


<div id="et-main-area" class="bloxx_et_builder">
    <header class="custom_header" id="main-header" style="top: 57px;">
        <div class="header_resp"></div>
    </header>

    <div id="main-content"> 
        <div class="container">
            <div id="content-area" class="clearfix">
                <div id="left-area" style="margin: 0; padding: 0;width: 100%;">
                    <!-- //sidebar  --> 
                    <?php while (have_posts()) : the_post(); ?>
                        <?php $post_id = get_the_id(); ?>
                        <article id="post-<?php the_ID(); ?>" <?php post_class('et_pb_post'); ?> style="margin: 0;">    
                            <div class="entry-content">
                                <div class="contentWrapper inside" id="category-page">
                                    <div class="builder_desktop_sidebar">

                                        <style>
                                            .switch {
                                                position: relative;
                                                display: inline-block;
                                                width: 30px;
                                                height: 17px;
                                            }

                                            .switch input { 
                                                opacity: 0;
                                                width: 0;
                                                height: 0;
                                            }

                                            .slider {
                                                position: absolute;
                                                cursor: pointer;
                                                top: 0;
                                                left: -3px;
                                                right: 0;
                                                bottom: 0;
                                                background-color: #ccc;
                                                -webkit-transition: .4s;
                                                transition: .4s;
                                            }

                                            .slider:before {
                                                position: absolute;
                                                content: "";
                                                height: 13px;
                                                width: 13px;
                                                left: 0px;
                                                bottom: 1px;
                                                background-color: #930abc;
                                                -webkit-transition: .4s;
                                                transition: .4s;
                                                border: 1px solid #fff;
                                            }

                                            input:checked + .slider {
                                                background-color: #fff;
                                            }

                                            input:focus + .slider {
                                                box-shadow: 0 0 1px #2196F3;
                                            }

                                            input:checked + .slider:before {
                                                -webkit-transform: translateX(11px);
                                                -ms-transform: translateX(11px);
                                                transform: translateX(11px);
                                            }

                                            /* Rounded sliders */
                                            .slider.round {
                                                border-radius: 17px;
                                            }

                                            .slider.round:before {
                                                border-radius: 50%;
                                            }
                                        </style>


                                        <div class="left-aside">
                                            <ul class="left-list">
                                                <li>
                                                    <a href="javascript:void(0)" title="Kitz" class="left-list-img">
                                                        <img src="<?php echo esc_url(kitz_url.'images/Sidebar-dash-icon.png'); ?>" alt="Bloxx" width="50">
                                                    </a>        
                                                </li>

                                                <li class="switch-sidebar">
                                                    <a data-type="page" href="javascript:void(0)" title="Add New" data-img="<?php echo esc_url(kitz_url.'images/pages.png'); ?>" data-old-img="<?php echo esc_url(kitz_url.'images/pages-actiavte.png'); ?>">
                                                        <img src="<?php echo esc_url(kitz_url.'images/pages-actiavte.png'); ?>" alt="Upload">
                                                    </a>
                                                </li>

                                                <li class="open-sidebar-layouts">
                                                    <a data-type="layout"  href="javascript:void(0)" title="Add New Layout" data-img="<?php echo esc_url(kitz_url.'images/layout.png'); ?>" data-old-img="<?php echo esc_url(kitz_url.'images/ayout-activate.png'); ?>">
                                                        <img src="<?php echo esc_url(kitz_url.'images/layout-activate.png'); ?>" alt="Upload">
                                                    </a>
                                                </li>

                                                <li class="open-sidebar">
                                                    <a data-type="section" href="javascript:void(0);" title="Add Section" data-img="<?php echo esc_url(kitz_url.'images/assetsnew.png'); ?>" data-old-img="<?php echo esc_url(kitz_url.'images/asset-activate.png'); ?>">
                                                        <img src="<?php echo esc_url(kitz_url.'images/asset-activate.png'); ?>" alt="Kitz">
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>


                                        <!-- User Pages When click on project pancil icon -->

                                        <div class="left-category-aside" id="left_project">
                                            <div class="wrapCategoryMenu">
                                                <ul class="builder_categories websites_pages">                                                    
                                                    <h2 class="heading2 text-white">Pages</h2>
                                                    <?php
                                                    $user = wp_get_current_user();
                                                    $current_user_id = $user->ID;
                                                    $args = array(
                                                        'post_type' => 'page',
                                                        'sort_order' => 'asc',
                                                        'sort_column' => 'post_title',
                                                        'posts_per_page' => 10,
                                                        'post_status' => 'publish'
                                                    );

                                                    $query = new WP_Query($args);
                                                    ?>
                                                    <?php if ($query->have_posts()) { ?>
                                                        <?php while ($query->have_posts()) { ?>
                                                            <?php
                                                            $query->the_post();
                                                            $switchid = get_the_id();
                                                            $switch_title = get_the_title();
                                                            $switch_url = get_the_permalink()."?kitz_builder=enable";
                                                            ?>
                                                            <?php if ($switchid == $post_id) { ?>
                                                                <li>
                                                                    <a href="javascript:void(0)" class="current_active"><?php echo esc_attr(get_the_title()); ?></a>
                                                                </li>
                                                            <?php } else { ?>
                                                                <li>
                                                                    <a href="<?php echo esc_attr($switch_url); ?>" data-id="<?php echo esc_html(sanitize_text_field($switchid)); ?>" data-nm="<?php echo esc_html(sanitize_text_field($post_id)); ?>" class="other_pages"><?php echo esc_html(sanitize_text_field(get_the_title())); ?></a>
                                                                </li>
                                                            <?php } ?>
                                                        <?php } ?>
                                                    <?php } else { ?>

                                                        <li>
                                                            <a href="javascript:void(0)" class="builder_cats builder_cat_active">No Page Found</a>                                                              
                                                        </li>
                                                    <?php } ?>
                                                    <li class="builder_page user_action">
                                                        <a href="javascript:void(0)" class="addNew add_page_restriction" data-name="<?php echo  esc_attr($term_id); ?>" data-title="builder">Add Blank Page <i class="fa fa-plus"></i></a>
                                                    </li>
                                                    <?php wp_reset_postdata(); ?>
                                                </ul>
                                            </div>
                                        </div>
                                        <!-- End User Pages When click on project pancil icon -->




                                        <!-- Get Cats By Args Query -->
                                        <!-- End Get Cats by Args Query -->




                                        <!-- All Section Displayed while click on plus icon -->
                                        <div class="left-category-aside" id="leftCategorySidebar">
                                            <div class="wrapCategoryMenu">
                                                <h2 class="heading2 text-white">Sections</h2>

                                                <ul class="builder_categories">
                                                    <!-- Section Load through Ajax -->
                                                </ul>
                                            </div>
                                        </div>

                                        <div class="sections_lists"><!-- Section lists --></div>


                                        <!-- End All Section Displayed while click on plus icon -->




                                        <!-- All Layout Displayed while click on plus icon -->
                                        <div class="left-category-aside" id="leftCategorySidebar_layouts">
                                            <div class="wrapCategoryMenu">
                                                <h2 class="heading2 text-white">Layouts</h2>
                                                <hr />
                                                <h2 class="heading2 text-white industry_h2_heading">Choose Industry</h2>
                                                <div class="layout_industries">
                                                    <!-- Load Layout Industries through Ajax -->
                                                </div>
                                                
                                                <ul class="builder_categories">
                                                    <!-- Layout Categories Load through Ajax -->
                                                </ul>
                                            </div>
                                        </div>

                                        <div class="layouts_lists"><!-- LAyout lists --></div>


                                        <!-- End All Layout Displayed while click on plus icon -->

                                    </div>


                                    <div class="wrapContent">
                                        <div class="topWrapmenu">
                                            <ul class="builder_bredcumbs">              
                                                <li><a href="javascript:void(0)"><?php echo esc_html(the_title()); ?></a></li>
                                                <li class="modeOption">
                                                    <a class="move_2divi" href="<?php echo esc_url(the_permalink()); ?>?et_fb=1&PageSpeed=off" title="Enable Divi Editor" id="<?php echo esc_html(sanitize_text_field($post_id)); ?>">
                                                        <span class="letter">D</span> Enable Divi Builder
                                                    </a>
                                                </li>
                                                <!-- <li><span>NewTestApp</span></li>    -->
                                            </ul>           

                                            <div class="see_global">
                                                Global Assets: <input type="checkbox" id="global_radio" /><label class="assets_global" for="global_radio">Assets</label>
                                            </div>

                                            <ul class="project_details_menu" id="slideNav"> 

                                            </ul>
                                            <ul class="headerButton">
                                                <li class="builder_layout_save">
                                                    <a href="javascript:void(0)" data-id="<?php echo esc_html(sanitize_text_field($post_id)); ?>" title="Save">
                                                        <img src="<?php echo esc_url(kitz_url.'images/floppy-icon.png'); ?>" alt="Save"> Save</a>
                                                </li>
                                            </ul>
                                            <ul class="topMenuUser">
                                                <li class="builder_layout_exit">
                                                    <a href="<?php echo esc_url(the_permalink()); ?>" data-id="<?php echo esc_html(sanitize_text_field($post_id)); ?>" class="exit_builder" title="Exit builder">
                                                        <img src="<?php echo esc_url(kitz_url.'images/doorway.png'); ?>" alt="Close"> Exit</a>
                                                </li>
                                            </ul>
                                        </div>


                                        <div class="builder_create_template variation_desktop">
                                            <script>
                                                jQuery(function ($) {
                                                    var changed_array = [];
                                                    $(".builder_inner_dropable .card > .builder-dragpost").each(function () {
                                                        var get_content = $(this).attr('id')
                                                        changed_array.push(get_content);
                                                    });
                                                    $("#section_count_default").val(changed_array);
                                                });
                                            </script>
                                            

                                            <!-- Header Data -->
                                            <div class="header_resp">

                                            </div>
                                            <!-- End Header Data -->


                                            <!-- Body Dragable Data -->
                                            <div class="builder_inner_dropable connectedSortable ui-sortable">

                                                <?php $post_content = get_the_content(); ?>

                                                <?php if ($post_content == "") { ?>
                                                    <div class="dropable_area test">
                                                            <h1><span><i class="fas fa-expand-arrows-alt"></i></span>Drag & Drop <br> Sections</h1>                 
                                                    </div>

                                                <?php } else { ?>

                                                    <?php
                                                    $page_content = get_the_content();
                                                    $explode_content = explode("[et_pb_section", $page_content);
                                                    $pg = (rand(-10, -100));
                                                    $array_number=0;
                                                    foreach ($explode_content as $pg_content) {
                                                        if ($pg_content != "") {
                                                            $page_shortcode = esc_html("[et_pb_section" . $pg_content);
                                                            ?>

                                                            <div class="card">
                                                                <div class="action_btns">
                                                                    <a href="javascript:void(0)" class="builder_uparrow" id="<?php echo esc_html($pg); ?>"><?php echo esc_html('&#8593;'); ?></a>
                                                                    <a href="javascript:void(0)" class="builder_downarrow" id="<?php echo esc_html($pg); ?>"><?php echo esc_html('&#8595;'); ?></a>
                                                                    
                                                                    <a href="javascript:void(0)" class="builder_remove_layout" id="<?php echo esc_html($pg); ?>"><i class="far fa-trash-alt" aria-hidden="true"></i></a>
                                                                </div>

                                                                <div class="builder-dragpost builder_<?php echo esc_html($pg); ?>" id="<?php echo esc_html($pg); ?>" data-id='<?php echo esc_html($term_id); ?>'>
                                                                    <div class="builder_inner_area">                                
                                                                        <input type="hidden" class="builder_layout" value="<?php echo esc_attr(strip_tags(htmlspecialchars($page_shortcode))); ?>"/>
                                                                        <div class="show_clone_html">

                                                                            <?php if ( is_plugin_active( 'divi-builder/divi-builder.php' ) ) { ?>
                                                                                <div id="et-boc" class="et-boc">
                                                                                    <div id="et_builder_outer_content" class="et_builder_outer_content">
                                                                                        <div class="et-l et-l--post">
                                                                                            <div class="et_builder_inner_content et_pb_gutters3">
                                                                                                <?php echo wp_kses_post(do_shortcode(html_entity_decode("$page_shortcode"))); ?>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            <?php } else { ?>
                                                                                <?php echo wp_kses_post(do_shortcode(html_entity_decode($page_shortcode))); ?>
                                                                            <?php } ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <?php $pg++; ?>
                                                            <?php $array_number++; ?>
                                                        <?php } ?>
                                                    <?php } ?>
                                                <?php } ?>
                                            </div>

                                            <!-- End Body Dragable Data -->
                                        </div>
                                    </div>
                                </div>
                            </div> <!-- .et_post_meta_wrapper -->
                        </article> <!-- .et_pb_post -->

                    <?php endwhile; ?>

                </div> <!-- #left-area -->

            </div> <!-- #content-area -->
        </div> <!-- .container -->

    </div> <!-- #main-content -->
    
    
    <footer class="custom_footer" style="top: 57px;">
        <div class="footer_resp"></div>
    </footer>

</div>



<?php get_footer(); ?>

<script>
jQuery(function($){
    $("body").addClass("enable_neo_builder");
    if($("header").hasClass('et-l et-l--header')) {
        $(".bloxx_et_builder header .header_resp").hide();
        $(".bloxx_et_builder footer .footer_resp").hide();
    } else {
        var get_header=$("#main-header").html();
        var get_footer=$("#main-footer").html();
        $(".bloxx_et_builder #main-header").show();        
        $(".bloxx_et_builder header .header_resp").html(get_header);
        $(".bloxx_et_builder footer").attr('id', 'main-footer')
        $(".bloxx_et_builder footer .footer_resp").html(get_footer);
        $(".bloxx_et_builder .custom_footer").show();
    }
});
</script>