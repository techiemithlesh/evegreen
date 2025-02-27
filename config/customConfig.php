<?php

return[
    "transportType"=>[
        "For Delivery"=>4,
        "For Godown"=>3,
    ],
    "bagStatus"=>[
        1=>"in factory",
        2=>"in godown",
        3=>"in transport",
        4=>"dispatched",
    ],
    "rollImportCsvHeader"=>['vendor_name',
                             "roll_no",
                            'vehicle_no',
                            "vendor_roll_no", 
                            'purchase_date',
                            "quality", 
                            'roll_size',
                            "roll_type",
                            "hardness",
                            "roll_gsm",
                            "bopp",
                            "roll_color",
                            "roll_length",
                            "net_weight",
                            "gross_weight"
    ],

    "orderImportCsvHeader"=>[
                        "order_no",
                        "order_date",
                        "client_name",
                        "estimate_delivery_date",
                        "bag_type",
                        "bag_quality",
                        "bag_gsm",
                        "units",
                        "total_units",
                        "rate_per_unit",
                        "bag_w",
                        "bag_l",
                        "bag_g",
                        "bag_loop_color",
                        "bag_color",
                        "booked_units",
                        "rate_type",
                        "fare_type",
                        "stereo_type",
                        "bag_printing_color",
                        "agent_name",
                        "alt_bag_color",
                        "alt_bag_gsm",
                        "is_delivered"      
    ],
    "bagTypeIdByShortName"=>[
        "D"=>1,
        "B"=>2,
        "U"=>3,
        "L"=>4,
        "LBB"=>5
    ],

    "orderRollMapCsvHeader"=>[
                        "order_no",
                        "roll_no",     
    ],
];