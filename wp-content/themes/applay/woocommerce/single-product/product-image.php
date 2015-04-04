<?php
/**
 * Single Product Image
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.14
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post, $woocommerce, $product;

$devide = get_post_meta($post->ID,'devide',true);
$orientation ='';
if($devide!='def_themeoption' && $devide!='def' && $devide!='' ){
	$devide_color = get_post_meta($post->ID,'devide_color_'.$devide,true);
	$orientation = get_post_meta($post->ID,'orientation',true);
}elseif($devide=='def_themeoption' || $devide==''){
	$devide = ot_get_option('devide','iphone5s');
	$devide_color = ot_get_option('devide_color_'.$devide,'silver');
	if($devide!='def'){
	$orientation = ot_get_option('orientation','0');
	}
}

?>
<div class="images <?php if($orientation=='1'){ echo esc_attr('landscape-screenshot');} ?>">

	<?php
		$images = $product->get_gallery_attachment_ids();
		$attachment_count = count( $product->get_gallery_attachment_ids() );
		if ( has_post_thumbnail() || $attachment_count > 0) {

			$image_title = esc_attr( get_the_title( get_post_thumbnail_id() ) );
			$image_link  = wp_get_attachment_url( get_post_thumbnail_id() );
			$image       = get_the_post_thumbnail( $post->ID, apply_filters( 'single_product_large_thumbnail_size', 'large' ), array(
				'title' => $image_title
				) );

			
			?>
            
			<?php 
			if ( $attachment_count > 0 ) {
				if(class_exists('iAppShowcase') && $devide!='def'){
					$devide_item = array(
						'devide' => $devide,
						'devide_color_'.$devide => $devide_color,
						'orientation' => $orientation,
						'content' => 'carousel',
						'content_carousel' => implode(',',$images),
					);
					echo iAppShowcase::ias_devide($devide_item);
				}else{
				?>
            	<div class="is-carousel single-carousel post-gallery content-image carousel-has-control product-ct" id="post-gallery-<?php the_ID() ?>" data-navigation=1>
				<?php
					wp_enqueue_style( 'lightbox2', get_template_directory_uri() . '/js/colorbox/colorbox.css');
					wp_enqueue_script( 'colorbox', get_template_directory_uri() . '/js/colorbox/jquery.colorbox-min.js', array('jquery'), '', true );
                foreach($images as $attachment_id){
                    $image_custom = wp_get_attachment_image_src( $attachment_id, 'large' ); ?>
                    <div class="single-gallery-item single-gallery-item-<?php echo esc_attr($attachment_id) ?>">
                        <a href="<?php echo esc_url(get_permalink($attachment_id)); ?>" class="colorbox-grid" data-rel="post-gallery-<?php the_ID() ?>" data-content=".single-gallery-item-<?php echo esc_attr($attachment_id) ?>">
                        <img src='<?php echo esc_url($image_custom[0]); ?>'>
                        </a>
                        <div class="hidden">
                            <div class="popup-data dark-div">
                                <?php $thumbnail = wp_get_attachment_image_src($attachment_id,'full', true); ?>
                                <img src="<?php echo esc_url($thumbnail[0]) ?>" width="<?php echo esc_attr($thumbnail[1]) ?>" height="<?php echo esc_attr($thumbnail[2]) ?>" title="<?php the_title_attribute(); ?>" alt="<?php the_title_attribute(); ?>">
                                <div class="popup-data-content">
                                    <h5><a href="<?php echo esc_url(get_permalink($attachment_id)); ?>" title="<?php the_title_attribute(); ?>"><?php echo get_the_title($attachment_id); ?></a></h5>
                                </div>
                            </div>
                        </div><!--/hidden-->
                    </div>
                <?php }//foreach attachments ?>
                </div><!--/is-carousel-->

            	<?php
				}//else class exists
				//$gallery = '[product-gallery]';
			} else {
			$gallery = '';
			//echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<a href="%s" itemprop="image" class="woocommerce-main-image zoom" title="%s" data-rel="prettyPhoto' . $gallery . '">%s</a>', $image_link, $image_title, $image ), $post->ID );
			wp_enqueue_style( 'lightbox2', get_template_directory_uri() . '/js/colorbox/colorbox.css');
			wp_enqueue_script( 'colorbox', get_template_directory_uri() . '/js/colorbox/jquery.colorbox-min.js', array('jquery'), '', true );
			?>
            <div class="is-carousel single-carousel post-gallery content-image carousel-has-control product-ct" id="post-gallery-<?php the_ID() ?>" data-navigation=1>
            	<div class="single-gallery-item single-gallery-item-<?php echo esc_attr(get_post_thumbnail_id()) ?>">
                        <a href="<?php echo esc_url(get_permalink(get_post_thumbnail_id())); ?>" class="colorbox-grid" data-rel="post-gallery-<?php the_ID() ?>" data-content=".single-gallery-item-<?php echo esc_attr(get_post_thumbnail_id()) ?>">
                        <img src='<?php echo esc_url(wp_get_attachment_url(get_post_thumbnail_id())); ?>'>
                        </a>
                        <div class="hidden">
                            <div class="popup-data dark-div">
                                <?php $thumbnail = wp_get_attachment_image_src(get_post_thumbnail_id(),'full', true); ?>
                                <img src="<?php echo esc_url($thumbnail[0]) ?>" width="<?php echo esc_attr($thumbnail[1]) ?>" height="<?php echo esc_attr($thumbnail[2]) ?>" title="<?php the_title_attribute(); ?>" alt="<?php the_title_attribute(); ?>">
                                <div class="popup-data-content">
                                    <h5><a href="<?php echo esc_url(get_permalink(get_post_thumbnail_id())); ?>" title="<?php the_title_attribute(); ?>"><?php echo get_the_title(get_post_thumbnail_id()); ?></a></h5>
                                </div>
                            </div>
                        </div><!--/hidden-->
                    </div>
            </div>
            <?php
			}

		} else {

			echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<img src="%s" alt="Placeholder" />', wc_placeholder_img_src() ), $post->ID );

		}
	?>

	<?php //do_action( 'woocommerce_product_thumbnails' ); ?>

</div>
