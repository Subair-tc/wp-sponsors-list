<?php

/**
 Template for site notification page.
 You can copy this file into your theme directory /site-notifications/ folder to modify the contents.
*/

get_header(); 
?>
<div class="col-sm-12">
	<div class="container pull-left">
      
        <div class="rareCurate-nav">
            
            <div class="cont-sub-head settings-page-head">
                <a aria-expanded="false" aria-haspopup="true" role="button" data-toggle="dropdown" href="#" id="drop4">
                <i class="fa fa-users" aria-hidden="true"></i>
                <h1>ourSponsors</h1>
            </a>
            </div>
        </div>

        <div class="our-sponsors-outer">
        
<?php
$args = array(

    'post_type' => 'sponsors'
);

// The Query
$the_query = new WP_Query( $args );

// The Loop
if ( $the_query->have_posts() ) {;
	while ( $the_query->have_posts() ) {
		$the_query->the_post();
        $post_id = get_the_ID();
        $default_image = WP_SPONSORS_PLUGIN_URL.'/images/default-placeholder.png';
        $sponsor_website = get_post_meta($post_id,'website',true);
        $img_attr = array(
            'class' => "img img-responsive",
            'alt'   => get_the_title()
        );
		?>
            <div class="col-sm-4 no-margin">
              <?php
                    if( $sponsor_website  ) { ?>
                        <a href="<?php echo $sponsor_website; ?>" title="<?php echo get_the_title(); ?>" target="_blank"> 
                           <?php
                                if( has_post_thumbnail( ) ) {
                                    the_post_thumbnail( 'full',  $img_attr );
                                } else {
                                    echo '<img  class="img img-responsive" src="'.$default_image.'" alt="'.get_the_title().'>" />';
                                }
                           ?>
                             
                        </a>
                   <?php
                    } else { ?>
                        <?php
                             if( has_post_thumbnail( ) ) {
                                    the_post_thumbnail( 'full',  $img_attr );
                                } else {
                                    echo '<img  class="img img-responsive" src="'.$default_image.'" alt="'.get_the_title().'>" />';
                                }

                        ?>
                   <?php }
                ?>


            </div>


        <?php
	}
	wp_reset_postdata();
} else {
    echo 'no sponsors';
}
?>
</div>
</div></div>

 <?php 
 get_footer();