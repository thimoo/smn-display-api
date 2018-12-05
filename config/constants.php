<?php

return [

    /*
    |--------------------------------------------------------------------------
    | SwissMetNet constants
    |--------------------------------------------------------------------------
    |
    | List of all external informations about SwissMetNet and the API
    |
    */

    /**
     * API version
     */
    'api_version' => 'v1',

    /**
     * URI to retreive the CSV
     */
    // 'csv_target_url' => 'http://data.geo.admin.ch/ch.meteoschweiz.swissmetnet/VQHA69.csv',
    'csv_target_url' => 'http://data.swissmetnet-display.ch/VQJA15.LSSW.csv',

    /**
     * The max number of no-data values in a collection to show
     * the collection in profile. It more no-data is present in the set,
     * then the collection is hide
     */
    'max_number_no_data_to_show_collection' => 36,

    /**
     * The max number of continious no-data to hide the data and the
     * collection
     */
    'max_number_no_data_to_hide_data' => 36,

    /**
     * Number of max continious substituted values, after that the
     * values are set to no-data with zero value
     */
    'max_substituted_values' => 3,

    /**
     * Relative path from the storage root to the profile's
     * informtaion csv
     */
    'stations_infos_path' => 'csv/stations_infos.csv',

];
