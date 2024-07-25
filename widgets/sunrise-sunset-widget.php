<?php
class Sunrise_Sunset_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'sunrise_sunset_widget';
    }

    public function get_title() {
        return 'Sunrise and Sunset';
    }

    public function get_icon() {
        return 'eicon-clock';
    }

    public function get_categories() {
        return [ 'general' ];
    }

    protected function register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => 'Settings',
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'location',
            [
                'label' => 'Location (City)',
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'New York',
                'placeholder' => 'Enter a city name',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $location = esc_html($settings['location']);

        // Example static output (In practice, fetch data from an API)
        echo "<h3>Sunrise and Sunset Times for {$location}</h3>";
        echo "<p>Sunrise: 6:00 AM</p>";
        echo "<p>Sunset: 7:45 PM</p>";
    }
}