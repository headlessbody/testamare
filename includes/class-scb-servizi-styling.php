<?php
/**
 * SCB Servizi Styling Class
 * 
 * Handles the styling settings page and dynamic CSS generation
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class SCB_Servizi_Styling {
    /**
     * Constructor
     */
    public function __construct() {
        // Register hooks
        add_action('admin_menu', array($this, 'register_settings_page'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_styles'), 999);
    }

    /**
     * Register the settings page
     */
    public function register_settings_page() {
        add_submenu_page(
            'edit.php?post_type=servizi',
            __('Impostazioni di Stile', 'cpt-servizi'),
            __('Stile', 'cpt-servizi'),
            'manage_options',
            'scb-servizi-styling',
            array($this, 'render_settings_page')
        );
    }

    /**
     * Register settings
     */
    public function register_settings() {
        // Register setting
        register_setting(
            'scb_servizi_styling_options',
            'scb_servizi_styling',
            array($this, 'sanitize_settings')
        );

        // Register sections
        add_settings_section(
            'scb_servizi_styling_general',
            __('Impostazioni Generali', 'cpt-servizi'),
            array($this, 'render_general_section'),
            'scb-servizi-styling'
        );

        add_settings_section(
            'scb_servizi_styling_archive',
            __('Pagine Archivio', 'cpt-servizi'),
            array($this, 'render_archive_section'),
            'scb-servizi-styling-archive'
        );

        add_settings_section(
            'scb_servizi_styling_single',
            __('Pagine Singole', 'cpt-servizi'),
            array($this, 'render_single_section'),
            'scb-servizi-styling-single'
        );

        add_settings_section(
            'scb_servizi_styling_map',
            __('Mappa', 'cpt-servizi'),
            array($this, 'render_map_section'),
            'scb-servizi-styling-map'
        );

        // Register fields for General section
        $this->register_general_fields();
        
        // Register fields for Archive section
        $this->register_archive_fields();
        
        // Register fields for Single section
        $this->register_single_fields();
        
        // Register fields for Map section
        $this->register_map_fields();
    }
    
    /**
     * Register fields for General section
     */
    private function register_general_fields() {
        // Primary Color
        add_settings_field(
            'scb_primary_color',
            __('Colore Primario', 'cpt-servizi'),
            array($this, 'render_color_field'),
            'scb-servizi-styling',
            'scb_servizi_styling_general',
            array(
                'id' => 'primary_color',
                'name' => 'scb_servizi_styling[primary_color]',
                'default' => '#0073aa',
                'description' => __('Colore principale usato per pulsanti, link e titoli.', 'cpt-servizi')
            )
        );
        
        // Secondary Color
        add_settings_field(
            'scb_secondary_color',
            __('Colore Secondario', 'cpt-servizi'),
            array($this, 'render_color_field'),
            'scb-servizi-styling',
            'scb_servizi_styling_general',
            array(
                'id' => 'secondary_color',
                'name' => 'scb_servizi_styling[secondary_color]',
                'default' => '#005177',
                'description' => __('Colore secondario usato per hover e accenti.', 'cpt-servizi')
            )
        );
        
        // Text Color
        add_settings_field(
            'scb_text_color',
            __('Colore Testo', 'cpt-servizi'),
            array($this, 'render_color_field'),
            'scb-servizi-styling',
            'scb_servizi_styling_general',
            array(
                'id' => 'text_color',
                'name' => 'scb_servizi_styling[text_color]',
                'default' => '#333333',
                'description' => __('Colore del testo principale.', 'cpt-servizi')
            )
        );
        
        // Background Color
        add_settings_field(
            'scb_background_color',
            __('Colore Sfondo', 'cpt-servizi'),
            array($this, 'render_color_field'),
            'scb-servizi-styling',
            'scb_servizi_styling_general',
            array(
                'id' => 'background_color',
                'name' => 'scb_servizi_styling[background_color]',
                'default' => '#f9f9f9',
                'description' => __('Colore di sfondo per box e contenitori.', 'cpt-servizi')
            )
        );
        
        // Border Color
        add_settings_field(
            'scb_border_color',
            __('Colore Bordi', 'cpt-servizi'),
            array($this, 'render_color_field'),
            'scb-servizi-styling',
            'scb_servizi_styling_general',
            array(
                'id' => 'border_color',
                'name' => 'scb_servizi_styling[border_color]',
                'default' => '#dddddd',
                'description' => __('Colore dei bordi per box e contenitori.', 'cpt-servizi')
            )
        );
        
        // Border Radius
        add_settings_field(
            'scb_border_radius',
            __('Raggio Bordi', 'cpt-servizi'),
            array($this, 'render_range_field'),
            'scb-servizi-styling',
            'scb_servizi_styling_general',
            array(
                'id' => 'border_radius',
                'name' => 'scb_servizi_styling[border_radius]',
                'min' => 0,
                'max' => 20,
                'step' => 1,
                'default' => 4,
                'unit' => 'px',
                'description' => __('Raggio degli angoli per box e pulsanti.', 'cpt-servizi')
            )
        );
        
        // Font Size
        add_settings_field(
            'scb_font_size',
            __('Dimensione Font Base', 'cpt-servizi'),
            array($this, 'render_range_field'),
            'scb-servizi-styling',
            'scb_servizi_styling_general',
            array(
                'id' => 'font_size',
                'name' => 'scb_servizi_styling[font_size]',
                'min' => 12,
                'max' => 20,
                'step' => 1,
                'default' => 14,
                'unit' => 'px',
                'description' => __('Dimensione del testo base.', 'cpt-servizi')
            )
        );
        
        // Line Height
        add_settings_field(
            'scb_line_height',
            __('Altezza Linea', 'cpt-servizi'),
            array($this, 'render_range_field'),
            'scb-servizi-styling',
            'scb_servizi_styling_general',
            array(
                'id' => 'line_height',
                'name' => 'scb_servizi_styling[line_height]',
                'min' => 1.0,
                'max' => 2.0,
                'step' => 0.1,
                'default' => 1.6,
                'unit' => '',
                'description' => __('Altezza linea per il testo.', 'cpt-servizi')
            )
        );
    }
    
    /**
     * Register fields for Archive section
     */
    private function register_archive_fields() {
        // Archive Grid Gap
        add_settings_field(
            'scb_archive_grid_gap',
            __('Spazio tra elementi', 'cpt-servizi'),
            array($this, 'render_range_field'),
            'scb-servizi-styling-archive',
            'scb_servizi_styling_archive',
            array(
                'id' => 'archive_grid_gap',
                'name' => 'scb_servizi_styling[archive_grid_gap]',
                'min' => 10,
                'max' => 50,
                'step' => 5,
                'default' => 20,
                'unit' => 'px',
                'description' => __('Spazio tra gli elementi nella griglia.', 'cpt-servizi')
            )
        );
        
        // Archive Item Border Width
        add_settings_field(
            'scb_archive_item_border_width',
            __('Spessore Bordo', 'cpt-servizi'),
            array($this, 'render_range_field'),
            'scb-servizi-styling-archive',
            'scb_servizi_styling_archive',
            array(
                'id' => 'archive_item_border_width',
                'name' => 'scb_servizi_styling[archive_item_border_width]',
                'min' => 0,
                'max' => 5,
                'step' => 1,
                'default' => 1,
                'unit' => 'px',
                'description' => __('Spessore del bordo degli elementi.', 'cpt-servizi')
            )
        );
        
        // Archive Item Border Radius
        add_settings_field(
            'scb_archive_item_border_radius',
            __('Raggio Bordo', 'cpt-servizi'),
            array($this, 'render_range_field'),
            'scb-servizi-styling-archive',
            'scb_servizi_styling_archive',
            array(
                'id' => 'archive_item_border_radius',
                'name' => 'scb_servizi_styling[archive_item_border_radius]',
                'min' => 0,
                'max' => 20,
                'step' => 1,
                'default' => 4,
                'unit' => 'px',
                'description' => __('Raggio degli angoli degli elementi.', 'cpt-servizi')
            )
        );
        
        // Archive Item Box Shadow
        add_settings_field(
            'scb_archive_item_box_shadow',
            __('Ombra Box', 'cpt-servizi'),
            array($this, 'render_range_field'),
            'scb-servizi-styling-archive',
            'scb_servizi_styling_archive',
            array(
                'id' => 'archive_item_box_shadow',
                'name' => 'scb_servizi_styling[archive_item_box_shadow]',
                'min' => 0,
                'max' => 20,
                'step' => 1,
                'default' => 5,
                'unit' => 'px',
                'description' => __('Dimensione dell\'ombra degli elementi al passaggio del mouse.', 'cpt-servizi')
            )
        );
        
        // Archive Item Image Height
        add_settings_field(
            'scb_archive_item_image_height',
            __('Altezza Immagine', 'cpt-servizi'),
            array($this, 'render_range_field'),
            'scb-servizi-styling-archive',
            'scb_servizi_styling_archive',
            array(
                'id' => 'archive_item_image_height',
                'name' => 'scb_servizi_styling[archive_item_image_height]',
                'min' => 100,
                'max' => 300,
                'step' => 10,
                'default' => 200,
                'unit' => 'px',
                'description' => __('Altezza dell\'immagine in evidenza.', 'cpt-servizi')
            )
        );
        
        // Archive Item Title Font Size
        add_settings_field(
            'scb_archive_item_title_font_size',
            __('Dimensione Titolo', 'cpt-servizi'),
            array($this, 'render_range_field'),
            'scb-servizi-styling-archive',
            'scb_servizi_styling_archive',
            array(
                'id' => 'archive_item_title_font_size',
                'name' => 'scb_servizi_styling[archive_item_title_font_size]',
                'min' => 14,
                'max' => 24,
                'step' => 1,
                'default' => 18,
                'unit' => 'px',
                'description' => __('Dimensione del font per i titoli.', 'cpt-servizi')
            )
        );
        
        // Archive Item Title Color
        add_settings_field(
            'scb_archive_item_title_color',
            __('Colore Titolo', 'cpt-servizi'),
            array($this, 'render_color_field'),
            'scb-servizi-styling-archive',
            'scb_servizi_styling_archive',
            array(
                'id' => 'archive_item_title_color',
                'name' => 'scb_servizi_styling[archive_item_title_color]',
                'default' => '#333333',
                'description' => __('Colore del titolo degli elementi.', 'cpt-servizi')
            )
        );
        
        // Archive Item Meta Color
        add_settings_field(
            'scb_archive_item_meta_color',
            __('Colore Metadati', 'cpt-servizi'),
            array($this, 'render_color_field'),
            'scb-servizi-styling-archive',
            'scb_servizi_styling_archive',
            array(
                'id' => 'archive_item_meta_color',
                'name' => 'scb_servizi_styling[archive_item_meta_color]',
                'default' => '#666666',
                'description' => __('Colore dei metadati (categorie, zone, ecc.).', 'cpt-servizi')
            )
        );
        
        // Archive Item Button Background
        add_settings_field(
            'scb_archive_item_button_bg',
            __('Sfondo Pulsante', 'cpt-servizi'),
            array($this, 'render_color_field'),
            'scb-servizi-styling-archive',
            'scb_servizi_styling_archive',
            array(
                'id' => 'archive_item_button_bg',
                'name' => 'scb_servizi_styling[archive_item_button_bg]',
                'default' => '#0073aa',
                'description' => __('Colore di sfondo del pulsante "Leggi di più".', 'cpt-servizi')
            )
        );
        
        // Archive Item Button Text Color
        add_settings_field(
            'scb_archive_item_button_text',
            __('Colore Testo Pulsante', 'cpt-servizi'),
            array($this, 'render_color_field'),
            'scb-servizi-styling-archive',
            'scb_servizi_styling_archive',
            array(
                'id' => 'archive_item_button_text',
                'name' => 'scb_servizi_styling[archive_item_button_text]',
                'default' => '#ffffff',
                'description' => __('Colore del testo del pulsante "Leggi di più".', 'cpt-servizi')
            )
        );
    }
    
    /**
     * Register fields for Single section
     */
    private function register_single_fields() {
        // Single Page Top Padding
        add_settings_field(
            'scb_single_top_padding',
            __('Spazio Superiore', 'cpt-servizi'),
            array($this, 'render_range_field'),
            'scb-servizi-styling-single',
            'scb_servizi_styling_single',
            array(
                'id' => 'single_top_padding',
                'name' => 'scb_servizi_styling[single_top_padding]',
                'min' => 0,
                'max' => 200,
                'step' => 10,
                'default' => 100,
                'unit' => 'px',
                'description' => __('Spazio sopra il contenuto nelle pagine singole.', 'cpt-servizi')
            )
        );
        
        // Single Title Font Size
        add_settings_field(
            'scb_single_title_font_size',
            __('Dimensione Titolo', 'cpt-servizi'),
            array($this, 'render_range_field'),
            'scb-servizi-styling-single',
            'scb_servizi_styling_single',
            array(
                'id' => 'single_title_font_size',
                'name' => 'scb_servizi_styling[single_title_font_size]',
                'min' => 20,
                'max' => 48,
                'step' => 2,
                'default' => 32,
                'unit' => 'px',
                'description' => __('Dimensione del titolo nelle pagine singole.', 'cpt-servizi')
            )
        );
        
        // Single Title Color
        add_settings_field(
            'scb_single_title_color',
            __('Colore Titolo', 'cpt-servizi'),
            array($this, 'render_color_field'),
            'scb-servizi-styling-single',
            'scb_servizi_styling_single',
            array(
                'id' => 'single_title_color',
                'name' => 'scb_servizi_styling[single_title_color]',
                'default' => '#333333',
                'description' => __('Colore del titolo nelle pagine singole.', 'cpt-servizi')
            )
        );
        
        // Single Content Font Size
        add_settings_field(
            'scb_single_content_font_size',
            __('Dimensione Testo Contenuto', 'cpt-servizi'),
            array($this, 'render_range_field'),
            'scb-servizi-styling-single',
            'scb_servizi_styling_single',
            array(
                'id' => 'single_content_font_size',
                'name' => 'scb_servizi_styling[single_content_font_size]',
                'min' => 12,
                'max' => 20,
                'step' => 1,
                'default' => 16,
                'unit' => 'px',
                'description' => __('Dimensione del testo del contenuto.', 'cpt-servizi')
            )
        );
        
        // Single Content Line Height
        add_settings_field(
            'scb_single_content_line_height',
            __('Altezza Linea Contenuto', 'cpt-servizi'),
            array($this, 'render_range_field'),
            'scb-servizi-styling-single',
            'scb_servizi_styling_single',
            array(
                'id' => 'single_content_line_height',
                'name' => 'scb_servizi_styling[single_content_line_height]',
                'min' => 1.2,
                'max' => 2.0,
                'step' => 0.1,
                'default' => 1.6,
                'unit' => '',
                'description' => __('Altezza linea per il testo del contenuto.', 'cpt-servizi')
            )
        );
        
        // Single Sidebar Background
        add_settings_field(
            'scb_single_sidebar_bg',
            __('Sfondo Sidebar', 'cpt-servizi'),
            array($this, 'render_color_field'),
            'scb-servizi-styling-single',
            'scb_servizi_styling_single',
            array(
                'id' => 'single_sidebar_bg',
                'name' => 'scb_servizi_styling[single_sidebar_bg]',
                'default' => '#f9f9f9',
                'description' => __('Colore di sfondo per i box nella sidebar.', 'cpt-servizi')
            )
        );
        
        // Single Sidebar Border Color
        add_settings_field(
            'scb_single_sidebar_border',
            __('Colore Bordo Sidebar', 'cpt-servizi'),
            array($this, 'render_color_field'),
            'scb-servizi-styling-single',
            'scb_servizi_styling_single',
            array(
                'id' => 'single_sidebar_border',
                'name' => 'scb_servizi_styling[single_sidebar_border]',
                'default' => '#dddddd',
                'description' => __('Colore del bordo per i box nella sidebar.', 'cpt-servizi')
            )
        );
        
        // Single Sidebar Border Radius
        add_settings_field(
            'scb_single_sidebar_radius',
            __('Raggio Bordo Sidebar', 'cpt-servizi'),
            array($this, 'render_range_field'),
            'scb-servizi-styling-single',
            'scb_servizi_styling_single',
            array(
                'id' => 'single_sidebar_radius',
                'name' => 'scb_servizi_styling[single_sidebar_radius]',
                'min' => 0,
                'max' => 20,
                'step' => 1,
                'default' => 4,
                'unit' => 'px',
                'description' => __('Raggio degli angoli per i box nella sidebar.', 'cpt-servizi')
            )
        );
        
        // Single Sidebar Heading Color
        add_settings_field(
            'scb_single_sidebar_heading',
            __('Colore Titoli Sidebar', 'cpt-servizi'),
            array($this, 'render_color_field'),
            'scb-servizi-styling-single',
            'scb_servizi_styling_single',
            array(
                'id' => 'single_sidebar_heading',
                'name' => 'scb_servizi_styling[single_sidebar_heading]',
                'default' => '#333333',
                'description' => __('Colore dei titoli nella sidebar.', 'cpt-servizi')
            )
        );
        
        // Single Link Color
        add_settings_field(
            'scb_single_link_color',
            __('Colore Link', 'cpt-servizi'),
            array($this, 'render_color_field'),
            'scb-servizi-styling-single',
            'scb_servizi_styling_single',
            array(
                'id' => 'single_link_color',
                'name' => 'scb_servizi_styling[single_link_color]',
                'default' => '#0073aa',
                'description' => __('Colore dei link nelle pagine singole.', 'cpt-servizi')
            )
        );
        
        // Single Link Hover Color
        add_settings_field(
            'scb_single_link_hover',
            __('Colore Link Hover', 'cpt-servizi'),
            array($this, 'render_color_field'),
            'scb-servizi-styling-single',
            'scb_servizi_styling_single',
            array(
                'id' => 'single_link_hover',
                'name' => 'scb_servizi_styling[single_link_hover]',
                'default' => '#005177',
                'description' => __('Colore dei link al passaggio del mouse.', 'cpt-servizi')
            )
        );
        
        // Single Featured Image Border Radius
        add_settings_field(
            'scb_single_image_radius',
            __('Raggio Bordo Immagine', 'cpt-servizi'),
            array($this, 'render_range_field'),
            'scb-servizi-styling-single',
            'scb_servizi_styling_single',
            array(
                'id' => 'single_image_radius',
                'name' => 'scb_servizi_styling[single_image_radius]',
                'min' => 0,
                'max' => 20,
                'step' => 1,
                'default' => 0,
                'unit' => 'px',
                'description' => __('Raggio degli angoli per l\'immagine in evidenza.', 'cpt-servizi')
            )
        );
    }
    
    /**
     * Register fields for Map section
     */
    private function register_map_fields() {
        // Map Container Height
        add_settings_field(
            'scb_map_height',
            __('Altezza Mappa', 'cpt-servizi'),
            array($this, 'render_range_field'),
            'scb-servizi-styling-map',
            'scb_servizi_styling_map',
            array(
                'id' => 'map_height',
                'name' => 'scb_servizi_styling[map_height]',
                'min' => 300,
                'max' => 800,
                'step' => 50,
                'default' => 500,
                'unit' => 'px',
                'description' => __('Altezza del contenitore della mappa.', 'cpt-servizi')
            )
        );
        
        // Map Width Ratio
        add_settings_field(
            'scb_map_width_ratio',
            __('Rapporto Larghezza Mappa', 'cpt-servizi'),
            array($this, 'render_range_field'),
            'scb-servizi-styling-map',
            'scb_servizi_styling_map',
            array(
                'id' => 'map_width_ratio',
                'name' => 'scb_servizi_styling[map_width_ratio]',
                'min' => 50,
                'max' => 80,
                'step' => 5,
                'default' => 60,
                'unit' => '%',
                'description' => __('Percentuale di larghezza della mappa rispetto al contenitore.', 'cpt-servizi')
            )
        );
        
        // Map Border Color
        add_settings_field(
            'scb_map_border_color',
            __('Colore Bordo Mappa', 'cpt-servizi'),
            array($this, 'render_color_field'),
            'scb-servizi-styling-map',
            'scb_servizi_styling_map',
            array(
                'id' => 'map_border_color',
                'name' => 'scb_servizi_styling[map_border_color]',
                'default' => '#dddddd',
                'description' => __('Colore del bordo della mappa.', 'cpt-servizi')
            )
        );
        
        // Map Border Radius
        add_settings_field(
            'scb_map_border_radius',
            __('Raggio Bordo Mappa', 'cpt-servizi'),
            array($this, 'render_range_field'),
            'scb-servizi-styling-map',
            'scb_servizi_styling_map',
            array(
                'id' => 'map_border_radius',
                'name' => 'scb_servizi_styling[map_border_radius]',
                'min' => 0,
                'max' => 20,
                'step' => 1,
                'default' => 4,
                'unit' => 'px',
                'description' => __('Raggio degli angoli della mappa.', 'cpt-servizi')
            )
        );

        // Disable Geolocation
        add_settings_field(
            'scb_map_disable_geolocation',
            __('Disabilita Geolocalizzazione', 'cpt-servizi'),
            array($this, 'render_checkbox_field'),
            'scb-servizi-styling-map',
            'scb_servizi_styling_map',
            array(
                'id' => 'map_disable_geolocation',
                'name' => 'scb_servizi_styling[map_disable_geolocation]',
                'default' => '0',
                'description' => __('Se attivo, la mappa mostrerà l\'area del Mediterraneo invece di cercare la posizione dell\'utente.', 'cpt-servizi')
            )
        );

        // Disable Geolocation Cache
        add_settings_field(
            'scb_map_disable_geolocation_cache',
            __('Disabilita Cache Geolocalizzazione', 'cpt-servizi'),
            array($this, 'render_checkbox_field'),
            'scb-servizi-styling-map',
            'scb_servizi_styling_map',
            array(
                'id' => 'map_disable_geolocation_cache',
                'name' => 'scb_servizi_styling[map_disable_geolocation_cache]',
                'default' => '0',
                'description' => __('Se attivo, la posizione dell\'utente verrà ricalcolata ad ogni caricamento della pagina invece di usare una versione memorizzata.', 'cpt-servizi')
            )
        );

        // Geolocation Provider
        add_settings_field(
            'scb_map_geolocation_provider',
            __('Provider Geolocalizzazione', 'cpt-servizi'),
            array($this, 'render_select_field'),
            'scb-servizi-styling-map',
            'scb_servizi_styling_map',
            array(
                'id' => 'map_geolocation_provider',
                'name' => 'scb_servizi_styling[map_geolocation_provider]',
                'default' => 'ipinfo',
                'options' => array(
                    'ipinfo' => 'ipinfo.io (Richiede loc)',
                    'ipapi' => 'ip-api.com (Gratuito/Illimitato per non-commerciale)',
                ),
                'description' => __('Scegli il fornitore del servizio di geolocalizzazione tramite IP.', 'cpt-servizi')
            )
        );
        
        // Sidebar Background Color
        add_settings_field(
            'scb_map_sidebar_bg',
            __('Sfondo Sidebar', 'cpt-servizi'),
            array($this, 'render_color_field'),
            'scb-servizi-styling-map',
            'scb_servizi_styling_map',
            array(
                'id' => 'map_sidebar_bg',
                'name' => 'scb_servizi_styling[map_sidebar_bg]',
                'default' => '#f9f9f9',
                'description' => __('Colore di sfondo della sidebar dei dettagli.', 'cpt-servizi')
            )
        );
        
        // Sidebar Border Color
        add_settings_field(
            'scb_map_sidebar_border',
            __('Colore Bordo Sidebar', 'cpt-servizi'),
            array($this, 'render_color_field'),
            'scb-servizi-styling-map',
            'scb_servizi_styling_map',
            array(
                'id' => 'map_sidebar_border',
                'name' => 'scb_servizi_styling[map_sidebar_border]',
                'default' => '#dddddd',
                'description' => __('Colore del bordo della sidebar dei dettagli.', 'cpt-servizi')
            )
        );
        
        // Sidebar Border Radius
        add_settings_field(
            'scb_map_sidebar_radius',
            __('Raggio Bordo Sidebar', 'cpt-servizi'),
            array($this, 'render_range_field'),
            'scb-servizi-styling-map',
            'scb_servizi_styling_map',
            array(
                'id' => 'map_sidebar_radius',
                'name' => 'scb_servizi_styling[map_sidebar_radius]',
                'min' => 0,
                'max' => 20,
                'step' => 1,
                'default' => 4,
                'unit' => 'px',
                'description' => __('Raggio degli angoli della sidebar dei dettagli.', 'cpt-servizi')
            )
        );
        
        // Location Title Color
        add_settings_field(
            'scb_map_location_title_color',
            __('Colore Titolo Location', 'cpt-servizi'),
            array($this, 'render_color_field'),
            'scb-servizi-styling-map',
            'scb_servizi_styling_map',
            array(
                'id' => 'map_location_title_color',
                'name' => 'scb_servizi_styling[map_location_title_color]',
                'default' => '#0073aa',
                'description' => __('Colore del titolo della location nella sidebar.', 'cpt-servizi')
            )
        );
        
        // Location Title Border Color
        add_settings_field(
            'scb_map_location_title_border',
            __('Colore Bordo Titolo', 'cpt-servizi'),
            array($this, 'render_color_field'),
            'scb-servizi-styling-map',
            'scb_servizi_styling_map',
            array(
                'id' => 'map_location_title_border',
                'name' => 'scb_servizi_styling[map_location_title_border]',
                'default' => '#0073aa',
                'description' => __('Colore del bordo sotto il titolo della location.', 'cpt-servizi')
            )
        );
        
        // Service Title Color
        add_settings_field(
            'scb_map_service_title_color',
            __('Colore Titolo Servizio', 'cpt-servizi'),
            array($this, 'render_color_field'),
            'scb-servizi-styling-map',
            'scb_servizi_styling_map',
            array(
                'id' => 'map_service_title_color',
                'name' => 'scb_servizi_styling[map_service_title_color]',
                'default' => '#0073aa',
                'description' => __('Colore del titolo del servizio nella sidebar.', 'cpt-servizi')
            )
        );
        
        // Button Background Color
        add_settings_field(
            'scb_map_button_bg',
            __('Sfondo Pulsante', 'cpt-servizi'),
            array($this, 'render_color_field'),
            'scb-servizi-styling-map',
            'scb_servizi_styling_map',
            array(
                'id' => 'map_button_bg',
                'name' => 'scb_servizi_styling[map_button_bg]',
                'default' => '#0073aa',
                'description' => __('Colore di sfondo dei pulsanti nella mappa.', 'cpt-servizi')
            )
        );
        
        // Button Text Color
        add_settings_field(
            'scb_map_button_text',
            __('Colore Testo Pulsante', 'cpt-servizi'),
            array($this, 'render_color_field'),
            'scb-servizi-styling-map',
            'scb_servizi_styling_map',
            array(
                'id' => 'map_button_text',
                'name' => 'scb_servizi_styling[map_button_text]',
                'default' => '#ffffff',
                'description' => __('Colore del testo dei pulsanti nella mappa.', 'cpt-servizi')
            )
        );
        
        // Filter Dropdown Border Color
        add_settings_field(
            'scb_map_filter_border',
            __('Colore Bordo Filtri', 'cpt-servizi'),
            array($this, 'render_color_field'),
            'scb-servizi-styling-map',
            'scb_servizi_styling_map',
            array(
                'id' => 'map_filter_border',
                'name' => 'scb_servizi_styling[map_filter_border]',
                'default' => '#dddddd',
                'description' => __('Colore del bordo dei menu a tendina dei filtri.', 'cpt-servizi')
            )
        );
    }
    
    /**
     * Render a color picker field
     */
    public function render_color_field($args) {
        $id = $args['id'];
        $name = $args['name'];
        $default = $args['default'];
        $description = isset($args['description']) ? $args['description'] : '';
        
        // Get saved value
        $options = get_option('scb_servizi_styling', array());
        $value = isset($options[$id]) ? $options[$id] : $default;
        
        ?>
        <div class="scb-field-wrapper scb-color-field-wrapper">
            <input type="text" id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($name); ?>" value="<?php echo esc_attr($value); ?>" class="scb-color-picker" data-default="<?php echo esc_attr($default); ?>" />
            <?php if ($description) : ?>
                <p class="description"><?php echo esc_html($description); ?></p>
            <?php endif; ?>
        </div>
        <?php
    }
    
    /**
     * Render a checkbox field
     */
    public function render_checkbox_field($args) {
        $id = $args['id'];
        $name = $args['name'];
        $default = $args['default'];
        $description = isset($args['description']) ? $args['description'] : '';
        
        // Get saved value
        $options = get_option('scb_servizi_styling', array());
        $value = isset($options[$id]) ? $options[$id] : $default;
        
        ?>
        <div class="scb-field-wrapper scb-checkbox-field-wrapper">
            <input type="checkbox" id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($name); ?>" value="1" <?php checked($value, '1'); ?> />
            <?php if ($description) : ?>
                <p class="description"><?php echo esc_html($description); ?></p>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * Render a select field
     */
    public function render_select_field($args) {
        $id = $args['id'];
        $name = $args['name'];
        $default = $args['default'];
        $options_list = isset($args['options']) ? $args['options'] : array();
        $description = isset($args['description']) ? $args['description'] : '';
        
        // Get saved value
        $options = get_option('scb_servizi_styling', array());
        $value = isset($options[$id]) ? $options[$id] : $default;
        
        ?>
        <div class="scb-field-wrapper scb-select-field-wrapper">
            <select id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($name); ?>">
                <?php foreach ($options_list as $opt_value => $opt_label) : ?>
                    <option value="<?php echo esc_attr($opt_value); ?>" <?php selected($value, $opt_value); ?>><?php echo esc_html($opt_label); ?></option>
                <?php endforeach; ?>
            </select>
            <?php if ($description) : ?>
                <p class="description"><?php echo esc_html($description); ?></p>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * Render a range field
     */
    public function render_range_field($args) {
        $id = $args['id'];
        $name = $args['name'];
        $min = $args['min'];
        $max = $args['max'];
        $step = $args['step'];
        $default = $args['default'];
        $unit = isset($args['unit']) ? $args['unit'] : 'px';
        $description = isset($args['description']) ? $args['description'] : '';
        
        // Get saved value
        $options = get_option('scb_servizi_styling', array());
        $value = isset($options[$id]) ? $options[$id] : $default;
        
        ?>
        <div class="scb-field-wrapper scb-range-field-wrapper">
            <input type="range" id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($name); ?>" min="<?php echo esc_attr($min); ?>" max="<?php echo esc_attr($max); ?>" step="<?php echo esc_attr($step); ?>" value="<?php echo esc_attr($value); ?>" class="scb-range-input" data-default="<?php echo esc_attr($default); ?>" data-unit="<?php echo esc_attr($unit); ?>" />
            <span class="scb-range-value"><?php echo esc_html($value . $unit); ?></span>
            <?php if ($description) : ?>
                <p class="description"><?php echo esc_html($description); ?></p>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * Render the settings page
     */
    public function render_settings_page() {
        // Check user capabilities
        if (!current_user_can('manage_options')) {
            return;
        }

        // Get current tab
        $active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'general';

        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <h2 class="nav-tab-wrapper">
                <a href="?post_type=servizi&page=scb-servizi-styling&tab=general" class="nav-tab <?php echo $active_tab == 'general' ? 'nav-tab-active' : ''; ?>"><?php _e('Generale', 'cpt-servizi'); ?></a>
                <a href="?post_type=servizi&page=scb-servizi-styling&tab=archive" class="nav-tab <?php echo $active_tab == 'archive' ? 'nav-tab-active' : ''; ?>"><?php _e('Archivio', 'cpt-servizi'); ?></a>
                <a href="?post_type=servizi&page=scb-servizi-styling&tab=single" class="nav-tab <?php echo $active_tab == 'single' ? 'nav-tab-active' : ''; ?>"><?php _e('Pagine Singole', 'cpt-servizi'); ?></a>
                <a href="?post_type=servizi&page=scb-servizi-styling&tab=map" class="nav-tab <?php echo $active_tab == 'map' ? 'nav-tab-active' : ''; ?>"><?php _e('Mappa', 'cpt-servizi'); ?></a>
            </h2>

            <form method="post" action="options.php">
                <?php
                // Output security fields
                settings_fields('scb_servizi_styling_options');
                
                // Output setting sections based on active tab
                if ($active_tab == 'general') {
                    do_settings_sections('scb-servizi-styling');
                } elseif ($active_tab == 'archive') {
                    do_settings_sections('scb-servizi-styling-archive');
                } elseif ($active_tab == 'single') {
                    do_settings_sections('scb-servizi-styling-single');
                } elseif ($active_tab == 'map') {
                    do_settings_sections('scb-servizi-styling-map');
                }
                
                // Output save settings button
                submit_button(__('Salva Impostazioni', 'cpt-servizi'));
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Render the general section
     */
    public function render_general_section($args) {
        ?>
        <p><?php _e('Impostazioni generali di stile per tutti gli elementi.', 'cpt-servizi'); ?></p>
        <?php
    }

    /**
     * Render the archive section
     */
    public function render_archive_section($args) {
        ?>
        <p><?php _e('Personalizza lo stile delle pagine di archivio.', 'cpt-servizi'); ?></p>
        <?php
    }

    /**
     * Render the single section
     */
    public function render_single_section($args) {
        ?>
        <p><?php _e('Personalizza lo stile delle pagine singole.', 'cpt-servizi'); ?></p>
        <?php
    }

    /**
     * Render the map section
     */
    public function render_map_section($args) {
        ?>
        <p><?php _e('Personalizza lo stile della mappa e dei suoi elementi.', 'cpt-servizi'); ?></p>
        <?php
    }

    /**
     * Sanitize settings
     */
    public function sanitize_settings($input) {
        $sanitized_input = array();
        
        // Get default values
        $defaults = $this->get_default_settings();
        
        // Sanitize color fields
        $color_fields = array(
            'primary_color', 'secondary_color', 'text_color', 'background_color', 'border_color',
            'archive_item_title_color', 'archive_item_meta_color', 'archive_item_button_bg', 'archive_item_button_text',
            'single_title_color', 'single_sidebar_bg', 'single_sidebar_border', 'single_sidebar_heading', 
            'single_link_color', 'single_link_hover',
            'map_border_color', 'map_sidebar_bg', 'map_sidebar_border', 'map_location_title_color',
            'map_location_title_border', 'map_service_title_color', 'map_button_bg', 'map_button_text',
            'map_filter_border'
        );
        
        foreach ($color_fields as $field) {
            if (isset($input[$field])) {
                // Validate color (hex, rgb, rgba)
                if (preg_match('/^(\#[\da-f]{3}|\#[\da-f]{6}|rgba\((\d{1,3}),\s*(\d{1,3}),\s*(\d{1,3}),\s*(\d*(?:\.\d+)?)\)|rgb\((\d{1,3}),\s*(\d{1,3}),\s*(\d{1,3})\))$/i', $input[$field])) {
                    $sanitized_input[$field] = $input[$field];
                } else {
                    // If invalid, use default
                    $sanitized_input[$field] = $defaults[$field];
                    
                    // Add error message
                    add_settings_error(
                        'scb_servizi_styling',
                        'invalid_color',
                        sprintf(__('Colore non valido per %s. Utilizzato il valore predefinito.', 'cpt-servizi'), $field),
                        'error'
                    );
                }
            }
        }
        
        // Sanitize numeric fields
        $numeric_fields = array(
            'border_radius', 'font_size', 'line_height',
            'archive_grid_gap', 'archive_item_border_width', 'archive_item_border_radius', 
            'archive_item_box_shadow', 'archive_item_image_height', 'archive_item_title_font_size',
            'single_top_padding', 'single_title_font_size', 'single_content_font_size', 
            'single_content_line_height', 'single_sidebar_radius', 'single_image_radius',
            'map_height', 'map_width_ratio', 'map_border_radius', 'map_sidebar_radius'
        );
        
        foreach ($numeric_fields as $field) {
            if (isset($input[$field])) {
                // Convert to float for validation
                $value = (float) $input[$field];
                
                // Check if the value is within the allowed range
                switch ($field) {
                    case 'border_radius':
                        $min = 0; $max = 20;
                        break;
                    case 'font_size':
                        $min = 12; $max = 20;
                        break;
                    case 'line_height':
                        $min = 1.0; $max = 2.0;
                        break;
                    case 'archive_grid_gap':
                        $min = 10; $max = 50;
                        break;
                    case 'archive_item_border_width':
                        $min = 0; $max = 5;
                        break;
                    case 'archive_item_border_radius':
                        $min = 0; $max = 20;
                        break;
                    case 'archive_item_box_shadow':
                        $min = 0; $max = 20;
                        break;
                    case 'archive_item_image_height':
                        $min = 100; $max = 300;
                        break;
                    case 'archive_item_title_font_size':
                        $min = 14; $max = 24;
                        break;
                    case 'single_top_padding':
                        $min = 0; $max = 200;
                        break;
                    case 'single_title_font_size':
                        $min = 20; $max = 48;
                        break;
                    case 'single_content_font_size':
                        $min = 12; $max = 20;
                        break;
                    case 'single_content_line_height':
                        $min = 1.2; $max = 2.0;
                        break;
                    case 'single_sidebar_radius':
                        $min = 0; $max = 20;
                        break;
                    case 'single_image_radius':
                        $min = 0; $max = 20;
                        break;
                    case 'map_height':
                        $min = 300; $max = 800;
                        break;
                    case 'map_width_ratio':
                        $min = 50; $max = 80;
                        break;
                    case 'map_border_radius':
                        $min = 0; $max = 20;
                        break;
                    case 'map_sidebar_radius':
                        $min = 0; $max = 20;
                        break;
                    default:
                        $min = 0; $max = 100;
                }
                
                // Validate and sanitize
                if ($value >= $min && $value <= $max) {
                    $sanitized_input[$field] = $value;
                } else {
                    // If invalid, use default
                    $sanitized_input[$field] = $defaults[$field];
                    
                    // Add error message
                    add_settings_error(
                        'scb_servizi_styling',
                        'invalid_number',
                        sprintf(__('Valore numerico non valido per %s. Utilizzato il valore predefinito.', 'cpt-servizi'), $field),
                        'error'
                    );
                }
            }
        }

        // Sanitize checkbox fields
        $sanitized_input['map_disable_geolocation'] = isset($input['map_disable_geolocation']) && $input['map_disable_geolocation'] === '1' ? '1' : '0';
        $sanitized_input['map_disable_geolocation_cache'] = isset($input['map_disable_geolocation_cache']) && $input['map_disable_geolocation_cache'] === '1' ? '1' : '0';
        
        // Sanitize select fields
        if (isset($input['map_geolocation_provider'])) {
            $allowed_providers = array('ipinfo', 'ipapi');
            if (in_array($input['map_geolocation_provider'], $allowed_providers)) {
                $sanitized_input['map_geolocation_provider'] = $input['map_geolocation_provider'];
            } else {
                $sanitized_input['map_geolocation_provider'] = $defaults['map_geolocation_provider'];
            }
        }

        return $sanitized_input;
    }
    
    /**
     * Get default settings
     */
    private function get_default_settings() {
        return array(
            // General
            'primary_color' => '#0073aa',
            'secondary_color' => '#005177',
            'text_color' => '#333333',
            'background_color' => '#f9f9f9',
            'border_color' => '#dddddd',
            'border_radius' => 4,
            'font_size' => 14,
            'line_height' => 1.6,
            
            // Archive
            'archive_grid_gap' => 20,
            'archive_item_border_width' => 1,
            'archive_item_border_radius' => 4,
            'archive_item_box_shadow' => 5,
            'archive_item_image_height' => 200,
            'archive_item_title_font_size' => 18,
            'archive_item_title_color' => '#333333',
            'archive_item_meta_color' => '#666666',
            'archive_item_button_bg' => '#0073aa',
            'archive_item_button_text' => '#ffffff',
            
            // Single
            'single_top_padding' => 100,
            'single_title_font_size' => 32,
            'single_title_color' => '#333333',
            'single_content_font_size' => 16,
            'single_content_line_height' => 1.6,
            'single_sidebar_bg' => '#f9f9f9',
            'single_sidebar_border' => '#dddddd',
            'single_sidebar_radius' => 4,
            'single_sidebar_heading' => '#333333',
            'single_link_color' => '#0073aa',
            'single_link_hover' => '#005177',
            'single_image_radius' => 0,
            
            // Map
            'map_height' => 500,
            'map_width_ratio' => 60,
            'map_border_color' => '#dddddd',
            'map_border_radius' => 4,
            'map_sidebar_bg' => '#f9f9f9',
            'map_sidebar_border' => '#dddddd',
            'map_sidebar_radius' => 4,
            'map_location_title_color' => '#0073aa',
            'map_location_title_border' => '#0073aa',
            'map_service_title_color' => '#0073aa',
            'map_button_bg' => '#0073aa',
            'map_button_text' => '#ffffff',
            'map_filter_border' => '#dddddd',
            'map_disable_geolocation' => '0',
            'map_disable_geolocation_cache' => '0',
            'map_geolocation_provider' => 'ipinfo'
        );
    }

    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_admin_scripts($hook) {
        // Only enqueue on our settings page
        if ('servizi_page_scb-servizi-styling' !== $hook) {
            return;
        }

        // Enqueue color picker
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
        
        // Enqueue custom admin script
        wp_enqueue_script(
            'scb-servizi-admin',
            plugins_url('js/servizi-admin.js', dirname(__FILE__)),
            array('jquery', 'wp-color-picker'),
            '1.0.0',
            true
        );
    }

    /**
     * Enqueue frontend styles
     */
    public function enqueue_frontend_styles() {
        // Only enqueue on pages that use our styles
        if (!is_singular(array('servizi', 'location')) && 
            !is_post_type_archive(array('servizi', 'location')) && 
            !is_tax(array('servizi_categoria', 'servizi_tag', 'location_zona'))) {
            return;
        }
        
        // Get saved settings
        $settings = get_option('scb_servizi_styling', array());
        
        // If no settings, use defaults
        if (empty($settings)) {
            $settings = $this->get_default_settings();
        }
        
        // Generate CSS
        $css = $this->generate_css($settings);
        
        // Add inline style
        wp_register_style('scb-servizi-custom', false);
        wp_enqueue_style('scb-servizi-custom');
        wp_add_inline_style('scb-servizi-custom', $css);
    }
    
    /**
     * Generate CSS based on settings
     */
    private function generate_css($settings) {
        // Start CSS
        $css = "/* SCB Servizi Custom Styles */\n";
        
        // General styles
        $css .= $this->generate_general_css($settings);
        
        // Archive styles
        $css .= $this->generate_archive_css($settings);
        
        // Single styles
        $css .= $this->generate_single_css($settings);
        
        // Map styles
        $css .= $this->generate_map_css($settings);
        
        return $css;
    }
    
    /**
     * Generate general CSS
     */
    private function generate_general_css($settings) {
        $css = "\n/* General Styles */\n";
        
        // Primary color for links and buttons
        $css .= "a, .scb-servizi-service-title a, .scb-servizi-location-title { color: {$settings['primary_color']}; }\n";
        $css .= ".scb-archive-item-link, .scb-servizi-service-link, .scb-servizi-location-details-button, #scb-servizi-filter-button { background-color: {$settings['primary_color']}; }\n";
        
        // Secondary color for hover states
        $css .= "a:hover, .scb-servizi-service-title a:hover { color: {$settings['secondary_color']}; }\n";
        $css .= ".scb-archive-item-link:hover, .scb-servizi-service-link:hover, .scb-servizi-location-details-button:hover, #scb-servizi-filter-button:hover { background-color: {$settings['secondary_color']}; }\n";
        
        // Text color
        $css .= "body .scb-content, body .scb-servizi-location-description, body .scb-servizi-service-excerpt { color: {$settings['text_color']}; }\n";
        
        // Background color for boxes
        $css .= ".scb-categories, .scb-tags, .scb-service-locations, .scb-location-coordinates, .scb-location-zones, .scb-location-services { background-color: {$settings['background_color']}; }\n";
        
        // Border color
        $css .= ".scb-archive-item, .scb-servizi-map-container, #scb-servizi-map, #scb-servizi-location-details, .scb-servizi-filters select { border-color: {$settings['border_color']}; }\n";
        
        // Border radius
        $border_radius = $settings['border_radius'] . 'px';
        $css .= ".scb-archive-item, .scb-archive-item-link, .scb-categories, .scb-tags, .scb-service-locations, .scb-location-coordinates, .scb-location-zones, .scb-location-services, .scb-servizi-service-link, .scb-servizi-location-details-button, #scb-servizi-filter-button, .scb-servizi-filters select { border-radius: {$border_radius}; }\n";
        
        // Font size
        $font_size = $settings['font_size'] . 'px';
        $css .= "body .scb-content, body .scb-servizi-location-description, body .scb-servizi-service-excerpt, body .scb-archive-item-excerpt { font-size: {$font_size}; }\n";
        
        // Line height
        $line_height = $settings['line_height'];
        $css .= "body .scb-content, body .scb-servizi-location-description, body .scb-archive-item-excerpt { line-height: {$line_height}; }\n";
        
        return $css;
    }
    
    /**
     * Generate archive CSS
     */
    private function generate_archive_css($settings) {
        $css = "\n/* Archive Styles */\n";
        
        // Grid gap
        $grid_gap = $settings['archive_grid_gap'] . 'px';
        $css .= ".scb-archive-grid { grid-gap: {$grid_gap}; }\n";
        
        // Border width
        $border_width = $settings['archive_item_border_width'] . 'px';
        $css .= ".scb-archive-item { border-width: {$border_width}; }\n";
        
        // Border radius
        $border_radius = $settings['archive_item_border_radius'] . 'px';
        $css .= ".scb-archive-item, .scb-archive-item-image, .scb-archive-item-image img { border-radius: {$border_radius}; }\n";
        
        // Box shadow
        $box_shadow = $settings['archive_item_box_shadow'] . 'px';
        $css .= ".scb-archive-item:hover { box-shadow: 0 {$box_shadow} 15px rgba(0, 0, 0, 0.1); }\n";
        
        // Image height
        $image_height = $settings['archive_item_image_height'] . 'px';
        $css .= ".scb-archive-item-image { height: {$image_height}; }\n";
        
        // Title font size
        $title_font_size = $settings['archive_item_title_font_size'] . 'px';
        $css .= ".scb-archive-item-title { font-size: {$title_font_size}; }\n";
        
        // Title color
        $css .= ".scb-archive-item-title a { color: {$settings['archive_item_title_color']}; }\n";
        
        // Meta color
        $css .= ".scb-archive-item-meta { color: {$settings['archive_item_meta_color']}; }\n";
        
        // Button background
        $css .= ".scb-archive-item-link { background-color: {$settings['archive_item_button_bg']}; }\n";
        
        // Button text color
        $css .= ".scb-archive-item-link { color: {$settings['archive_item_button_text']}; }\n";
        
        return $css;
    }
    
    /**
     * Generate single CSS
     */
    private function generate_single_css($settings) {
        $css = "\n/* Single Page Styles */\n";
        
        // Top padding
        $top_padding = $settings['single_top_padding'] . 'px';
        $css .= ".scb-single-servizi, .scb-single-location { padding-top: {$top_padding}; }\n";
        
        // Title font size
        $title_font_size = $settings['single_title_font_size'] . 'px';
        $css .= ".scb-servizi-title, .scb-location-title { font-size: {$title_font_size}; }\n";
        
        // Title color
        $css .= ".scb-servizi-title, .scb-location-title { color: {$settings['single_title_color']}; }\n";
        
        // Content font size
        $content_font_size = $settings['single_content_font_size'] . 'px';
        $css .= ".scb-content { font-size: {$content_font_size}; }\n";
        
        // Content line height
        $content_line_height = $settings['single_content_line_height'];
        $css .= ".scb-content { line-height: {$content_line_height}; }\n";
        
        // Sidebar background
        $css .= ".scb-categories, .scb-tags, .scb-service-locations, .scb-location-coordinates, .scb-location-zones, .scb-location-services { background-color: {$settings['single_sidebar_bg']}; }\n";
        
        // Sidebar border color
        $css .= ".scb-categories, .scb-tags, .scb-service-locations, .scb-location-coordinates, .scb-location-zones, .scb-location-services { border: 1px solid {$settings['single_sidebar_border']}; }\n";
        
        // Sidebar border radius
        $sidebar_radius = $settings['single_sidebar_radius'] . 'px';
        $css .= ".scb-categories, .scb-tags, .scb-service-locations, .scb-location-coordinates, .scb-location-zones, .scb-location-services { border-radius: {$sidebar_radius}; }\n";
        
        // Sidebar heading color
        $css .= ".scb-categories h3, .scb-tags h3, .scb-service-locations h3, .scb-location-coordinates h3, .scb-location-zones h3, .scb-location-services h3 { color: {$settings['single_sidebar_heading']}; }\n";
        
        // Link color
        $css .= ".scb-categories a, .scb-tags a, .scb-service-locations a, .scb-location-zones a, .scb-location-services a { color: {$settings['single_link_color']}; }\n";
        
        // Link hover color
        $css .= ".scb-categories a:hover, .scb-tags a:hover, .scb-service-locations a:hover, .scb-location-zones a:hover, .scb-location-services a:hover { color: {$settings['single_link_hover']}; }\n";
        
        // Featured image border radius
        $image_radius = $settings['single_image_radius'] . 'px';
        $css .= ".scb-featured-image img { border-radius: {$image_radius}; }\n";
        
        return $css;
    }
    
    /**
     * Generate map CSS
     */
    private function generate_map_css($settings) {
        $css = "\n/* Map Styles */\n";
        
        // Map height
        $map_height = $settings['map_height'] . 'px';
        $css .= "#scb-servizi-map { min-height: {$map_height}; }\n";
        
        // Map width ratio
        $map_width = $settings['map_width_ratio'] . '%';
        $css .= "#scb-servizi-map { width: {$map_width}; }\n";
        
        // Map border color
        $css .= "#scb-servizi-map { border-color: {$settings['map_border_color']}; }\n";
        
        // Map border radius
        $map_radius = $settings['map_border_radius'] . 'px';
        $css .= "#scb-servizi-map { border-radius: {$map_radius}; }\n";
        
        // Sidebar background
        $css .= "#scb-servizi-location-details { background-color: {$settings['map_sidebar_bg']}; }\n";
        
        // Sidebar border color
        $css .= "#scb-servizi-location-details { border-color: {$settings['map_sidebar_border']}; }\n";
        
        // Sidebar border radius
        $sidebar_radius = $settings['map_sidebar_radius'] . 'px';
        $css .= "#scb-servizi-location-details { border-radius: {$sidebar_radius}; }\n";
        
        // Location title color
        $css .= ".scb-servizi-location-title { color: {$settings['map_location_title_color']}; }\n";
        
        // Location title border color
        $css .= ".scb-servizi-location-title { border-bottom-color: {$settings['map_location_title_border']}; }\n";
        
        // Service title color
        $css .= ".scb-servizi-service-title a { color: {$settings['map_service_title_color']}; }\n";
        
        // Button background color
        $css .= ".scb-servizi-service-link, .scb-servizi-location-details-button, #scb-servizi-filter-button { background-color: {$settings['map_button_bg']}; }\n";
        
        // Button text color
        $css .= ".scb-servizi-service-link, .scb-servizi-location-details-button, #scb-servizi-filter-button { color: {$settings['map_button_text']}; }\n";
        
        // Filter dropdown border color
        $css .= ".scb-servizi-filters select { border-color: {$settings['map_filter_border']}; }\n";
        
        return $css;
    }
}

// Initialize the class
$scb_servizi_styling = new SCB_Servizi_Styling();