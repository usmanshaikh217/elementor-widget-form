<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
    class Custom_Form_Widget extends \Elementor\Widget_Base {

        public function get_name() {
            return 'custom_form_widget';
        }

        public function get_title() {
            return __( 'Custom Form Widget', 'custom-elementor-widget' );
        }

        public function get_icon() {
            return 'eicon-form-horizontal';
        }

        public function get_categories() {
            return [ 'general' ];
        }

        protected function _register_controls() {
            $this->start_controls_section(
                'content_section',
                [
                    'label' => __( 'Content', 'custom-elementor-widget' ),
                    'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
                ]
            );

            // $this->add_control(
            //     'title',
            //     [
            //         'label' => __( 'Title', 'custom-elementor-widget' ),
            //         'type' => \Elementor\Controls_Manager::TEXT,
            //         'default' => __( 'Enter your title', 'custom-elementor-widget' ),
            //     ]
            // );
            
            // $this->add_control(
            //     'author_name',
            //     [
            //         'label' => __( 'Author Name', 'custom-elementor-widget' ),
            //         'type' => \Elementor\Controls_Manager::TEXT,
            //         'default' => __( 'Enter your Author Name', 'custom-elementor-widget' ),
            //     ]
            // );

            $this->end_controls_section();
        }

        protected function render() {
            ?>
            <form id="custom-submission-form" enctype="multipart/form-data">
                <div id="form-message"></div>
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" class="form-control" id="title" name="title" minlength="5" required>
                </div>
                <div class="form-group">
                    <label for="content">Content</label>
                    <textarea class="form-control" id="content" name="content" minlength="20" required ></textarea>
                </div>
                <div class="form-group">
                    <label for="featured_image">Featured Image</label>
                    <input type="file" class="form-control-file" id="featured_image" name="featured_image" accept=".jpg,.jpeg,.png">
                </div>
                <div class="form-group">
                    <label for="taxonomy">Category</label>
                    <?php
                    $terms = get_terms(array(
                        'taxonomy' => 'submission_category',
                        'hide_empty' => false,
                    ));
                    if (!empty($terms) && !is_wp_error($terms)) {
                        echo '<select class="form-control" id="taxonomy" name="taxonomy" required>';
                        echo '<option value="">Select Category</option>';
                        foreach ($terms as $term) {
                            echo '<option value="' . esc_attr($term->term_id) . '">' . esc_html($term->name) . '</option>';
                        }
                        echo '</select>';
                    }
                    ?>
                </div>
                <div class="form-group">
                    <label for="author_name">Author Name</label>
                    <input type="text" class="form-control" id="author_name" name="author_name" required>
                </div>
                <div class="form-group">
                    <label for="author_email">Author Email</label>
                    <input type="email" class="form-control" id="author_email" name="author_email" required>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
                <div id="form-message"></div>
            </form>

			<br><br>
			<!--fetch data -->
			<section>
				<div class="container">
					<div class="row">
						<?php $query = new WP_Query( [
								'post_type'      => 'submission',
								'nopaging'       => true,
								'posts_per_page' => '-1',
							] ); 
							if ( $query->have_posts() ) :
							while ( $query->have_posts() ) : $query->the_post(); 
							$author_name = get_post_meta(get_the_ID(), 'author_name', true);
							$author_email = get_post_meta(get_the_ID(), 'author_email', true); ?>
								<div class="col-md-6">
									<div class="card" style="width: 18rem;">
										<?php if(has_post_thumbnail()) { ?>
											<img src="<?php the_post_thumbnail_url(); ?>" class="card-img-top" alt="<?php the_title(); ?>">
										<?php } else { ?>
											<img src="https://codewithusman.itechavengers.com/wp-content/uploads/2024/07/logo-2.png" class="card-img-top" alt="<?php the_title(); ?>">
										<?php } ?>
										<div class="card-body">
											<h5 class="card-title"><?php the_title(); ?></h5>
											<p class="card-text"><?php the_content() ?></p>
											<p><small>Career Form</small> | <small><?php echo $author_name; ?></small></p>
											<a href="mailto:<?php echo $author_email; ?>" class="btn btn-primary">Contact Email</a>
										</div>
									</div>
								</div>
							<?php
								endwhile;
								endif;
								wp_reset_postdata();
							?>
					</div>
				</div>
			</section>
			

            <?php
        }

        protected function _content_template() {

        }
        
    }

