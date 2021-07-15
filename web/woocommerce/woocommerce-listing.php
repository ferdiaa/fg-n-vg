<?php
/**
 * @Woocommerce Product Listing
 * @return {}
 */
 
 

$content_div	= 'col-xs-12 col-sm-12 col-md-12 col-lg-12';
?>	
<div class="container">
    <div class="row">
        <div id="tg-twocolumns" class="tg-twocolumns">
        	<?php if( !empty( $product_subtitle ) || !empty( $product_description ) ) {?>
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-push-2 col-lg-8">
                    <div class="tg-sectionhead">
                        <?php if( !empty( $product_subtitle ) ) {?>
                        <div class="tg-sectiontitle">
                            <h2><?php echo esc_attr( $product_subtitle );?></h2>
                        </div>
                        <?php }?>
                        <?php if( !empty( $product_description ) ) {?>
                            <div class="tg-description">
                                <p><?php echo esc_attr( $product_description );?></p>
                            </div>
                        <?php }?>
                    </div>
                </div>
            <?php }?>
			<div class="<?php echo esc_attr( $content_div );?>">
			  <div id="tg-content" class="tg-content">
                <div class="tg-products tg-productsgrid">
                    <div class="tg-featureproducts">
						<div class="row">
							<?php 
								if ( have_posts() ) {
									  while ( have_posts() ) : the_post(); 
											get_template_part( 'woocommerce/layouts/grid', 'layout' ); 
									  endwhile; 
								} else{
									esc_html_e('No Product Found.','listingo');
								}
							?>
						</div>
					</div>
					<?php wc_get_template( 'loop/pagination.php' );?>
			    </div>
			   </div>
            </div>	
		</div>
	</div>
</div>
