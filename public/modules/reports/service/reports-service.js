angular.module('adminPortal.reports').factory('Reports', function ($http, $filter, Cookie) {

    var service = {};

    service.filterNone = function (val) {
        return val;
    };

    service.filterNumber = function (val) {
        return $filter('number')(val, 2);
    };

    service.filterDate = function (val) {
        return moment(val).format('MM/DD/YYYY');
    };

    service.time_periods = [
        {
            desc: 'Today',
            url: 'today'
        },
        {
            desc: 'Yesterday',
            url: 'yesterday'
        },
        {
            desc: 'This Week',
            url: 'current_week'
        },
        {
            desc: 'Last Week',
            url: 'previous_week'
        },
        {
            desc: 'Month to Date',
            url: 'current_month'
        },
        {
            desc: 'Year to Date',
            url: 'current_year'
        }
    ];

    service.period_type = 'today';

    service.groupBy = [
        {
            desc: 'Date',
            key: 'order_date'
        }, {
            desc: 'Hour of Day',
            key: 'order_hour'
        }, {
            desc: 'Day of Week',
            key: 'order_day_of_week'
        }, {
            desc: 'Month',
            key: 'order_month'
        }, {
            desc: 'Year',
            key: 'order_year'
        }, {
            desc: 'Order Type',
            key: 'order_type'
        }, {
            desc: 'Payment Type',
            key: 'payment_type'
        }];

    //Create a New web_skin Attribute
    service.post = function (url, post_data) {
        var post = $http.post('/reports/' + url, post_data);

        post.then(function (data) {
            return data;
        });
        return post;
    };

    //Delete a web_skin Attribute
    service.delete = function (url, id) {
        var destroy = $http.delete('/reports/' + url + '/' + id);

        destroy.then(function (data) {
            return data;
        });
        return destroy;
    };

    //Update a web_skin
    service.update = function (url, put_data) {
        var put = $http.put('/reports/' + url, put_data);

        put.then(function (data) {
            return data;
        });
        return put;
    };

    //Get Request
    service.get = function (url) {
        var get = $http.get('/reports/' + url);

        get.then(function (data) {
            return data;
        });
        return get;
    };


    service.demo_data = {};

    service.demo_data.today = {
        labels: [
            '9am',
            '10am',
            '11am',
            '12am',
            '1pm',
            '2pm',
            '3pm',
            '4pm',
            '5pm',
            '6pm',
            '7pm',
            '8pm'
        ],
        chart_values: [
            [
                25.45,
                152.75,
                145.15,
                229.25,
                108.95,
                155.35,
                20.1,
                56.25,
                38.4,
                42.1,
                20.1,
                7.75
            ],
            [
                3,
                4,
                17,
                22,
                10,
                9,
                3,
                1,
                4,
                4,
                1,
                1
            ]
        ],
        user_sales: [
            {
                user_id: 3369556,
                first_name: 'Alexis',
                last_name: 'Rebane',
                user_sales: '96.50'
            },
            {
                user_id: 6388867,
                first_name: 'Cameron',
                last_name: 'Hoag',
                user_sales: '68.55'
            },
            {
                user_id: 5863245,
                first_name: 'Dale',
                last_name: 'Meyers',
                user_sales: '56.25'
            },
            {
                user_id: 6155341,
                first_name: 'Sara',
                last_name: 'Hardenburgh',
                user_sales: '35.70'
            },
            {
                user_id: 2421271,
                first_name: 'Kenneth',
                last_name: 'Black',
                user_sales: '34.60'
            }
        ],
        item_sales: [],
        device_sales: {
            labels: [
                'android',
                'iphone',
                'web',
                'web-mobile'
            ],
            sales: [
                '104.75',
                '316.85',
                '537.50',
                '42.50'
            ]
        },
        returning_new: {
            labels: [
                'Returning',
                'New Customer'
            ],
            chart_values: [
                71,
                6
            ]
        },
        day_of_week: {
            labels: [
                'Today'
            ],
            sales: [
                '1001.60'
            ]
        }
    };

    service.demo_data.yesterday = {
        labels: [
            '8am',
            '9am',
            '10am',
            '11am',
            '12am',
            '1pm',
            '2pm',
            '3pm',
            '4pm',
            '5pm',
            '6pm'
        ],
        chart_values: [
            [
                11.75,
                108.2,
                214.8,
                332.45,
                246.7,
                100.75,
                20.9,
                15,
                15.5,
                45.45,
                23.9
            ],
            [
                1,
                1,
                8,
                29,
                19,
                9,
                2,
                1,
                1,
                2,
                2
            ]
        ],
        user_sales: [
            {
                user_id: 6195202,
                first_name: 'Kjell',
                last_name: 'Hedstrom',
                user_sales: '108.20'
            },
            {
                user_id: 1317777,
                first_name: 'Caitlin',
                last_name: 'Ascher',
                user_sales: '90.30'
            },
            {
                user_id: 6090697,
                first_name: 'Erik',
                last_name: 'Bartholomy',
                user_sales: '36.75'
            },
            {
                user_id: 6379955,
                first_name: 'Clayton',
                last_name: 'Shank',
                user_sales: '34.60'
            },
            {
                user_id: 6385552,
                first_name: 'Eric',
                last_name: 'Bloom',
                user_sales: '34.50'
            }
        ],
        item_sales: [],
        device_sales: {
            labels: [
                'android',
                'iphone',
                'web',
                'web-mobile'
            ],
            sales: [
                '158.30',
                '280.35',
                '667.85',
                '28.90'
            ]
        },
        returning_new: {
            labels: [
                'Returning',
                'New Customer'
            ],
            chart_values: [
                67,
                7
            ]
        },
        day_of_week: {
            labels: [
                'Thursday'
            ],
            sales: [
                '1135.40'
            ]
        }
    };

    service.demo_data.current_week = {
        labels: [
            '08/05',
            '08/06',
            '08/07',
            '08/08',
            '08/09',
            '08/10'
        ],
        chart_values: [
            [
                '421.35',
                '1133.15',
                '1304.10',
                '1000.65',
                '902.70',
                '1322.60'
            ],
            [
                17,
                60,
                71,
                63,
                88,
                90
            ]
        ],
        user_sales: [
            {
                user_id: 5708608,
                first_name: 'Casey',
                last_name: 'Primm',
                user_sales: '26.14'
            },
            {
                user_id: 5215689,
                first_name: 'Bambi',
                last_name: 'Leveritt',
                user_sales: '25.36'
            },
            {
                user_id: 6611611,
                first_name: 'Makaria',
                last_name: 'Reed',
                user_sales: '20.32'
            },
            {
                user_id: 5976724,
                first_name: 'Heather',
                last_name: 'Tumey ',
                user_sales: '18.17'
            },
            {
                user_id: 6161900,
                first_name: 'Colby',
                last_name: 'Moore',
                user_sales: '10.58'
            }
        ],
        item_sales: [
            {
                item_id: 299858,
                item_name: 'Drink & Chips',
                item_sales: '94.31'
            },
            {
                item_id: 299789,
                item_name: 'Turkey',
                item_sales: '86.51'
            },
            {
                item_id: 0,
                item_name: 'Promo',
                item_sales: '56.09'
            },
            {
                item_id: 299786,
                item_name: 'Ultimate Club',
                item_sales: '50.72'
            },
            {
                item_id: 299798,
                item_name: 'Chipolte Cheese Chicken',
                item_sales: '41.07'
            }
        ],
        device_sales: {
            labels: [
                'android',
                'iphone',
                'web',
                'web-mobile'
            ],
            sales: [
                '54.11',
                '7.99',
                '55.33',
                '7.02'
            ]
        },
        returning_new: {
            labels: [
                'Returning',
                'New Customer'
            ],
            chart_values: [
                254,
                62
            ]
        },
        day_of_week: {
            labels: [
                'Sunday',
                'Monday',
                'Tuesday',
                'Wednesday',
                'Thursday',
                'Friday'
            ],
            sales: [
                '298.35',
                '981.15',
                '1304.10',
                '1161.65',
                '972.70',
                '1116.60'
            ]
        }
    };

    service.demo_data.previous_week = {
        labels: [
            '07/30',
            '07/31',
            '08/01',
            '08/02',
            '08/03',
            '08/04'
        ],
        chart_values: [
            [
                '298.35',
                '981.15',
                '1304.10',
                '1161.65',
                '972.70',
                '1116.60'
            ],
            [
                16,
                65,
                78,
                59,
                63,
                75
            ]
        ],
        user_sales: [
            {
                user_id: 346949,
                first_name: 'JULIE',
                last_name: 'HURT',
                user_sales: '114.25'
            },
            {
                user_id: 7160399,
                first_name: 'Scott',
                last_name: '',
                user_sales: '114.25'
            },
            {
                user_id: 133501,
                first_name: 'Keri',
                last_name: 'Miller',
                user_sales: '108.10'
            },
            {
                user_id: 23358,
                first_name: 'Robert',
                last_name: 'Smith',
                user_sales: '101.95'
            },
            {
                user_id: 7075120,
                first_name: 'Adam',
                last_name: '',
                user_sales: '93.35'
            }
        ],
        item_sales: [
            {
                item_id: 276614,
                item_name: 'Turkey and Swiss',
                item_sales: '3114.50'
            },
            {
                item_id: 276633,
                item_name: 'Potato Chips',
                item_sales: '2474.70'
            },
            {
                item_id: 276612,
                item_name: 'Italian Sandwich',
                item_sales: '2335.15'
            },
            {
                item_id: 276648,
                item_name: 'Cookies',
                item_sales: '2259.95'
            },
            {
                item_id: 276962,
                item_name: 'Bottled Water',
                item_sales: '2169.10'
            }
        ],
        device_sales: {
            labels: [
                'android',
                'iphone',
                'web',
                'web-mobile'
            ],
            sales: [
                '680.40',
                '1454.35',
                '3521.15',
                '178.65'
            ]
        },
        returning_new: {
            labels: [
                'Returning',
                'New Customer'
            ],
            chart_values: [
                188,
                96
            ]
        },
        day_of_week: {
            labels: [
                'Sunday',
                'Monday',
                'Tuesday',
                'Wednesday',
                'Thursday',
                'Friday'
            ],
            sales: [
                '360.35',
                '1088.15',
                '1233.10',
                '945.65',
                '888.70',
                '1223.60'
            ]
        }
    };

    service.demo_data.current_month = {
        labels: [
            '07/31-08/06',
            '08/06-08/12',
        ],
        chart_values: [
            [
                '3078.60',
                '1322.34'
            ],
            [
                204,
                44
            ]
        ],
        user_sales: [
            {
                user_id: 7188680,
                first_name: 'Steve',
                last_name: '',
                user_sales: '84.55'
            },
            {
                user_id: 5575286,
                first_name: 'Whitney',
                last_name: 'Howe',
                user_sales: '82.85'
            },
            {
                user_id: 573386,
                first_name: 'Neal',
                last_name: 'Rogers',
                user_sales: '68.30'
            },
            {
                user_id: 815544,
                first_name: 'Todd',
                last_name: 'Edbrooke',
                user_sales: '60.50'
            },
            {
                user_id: 4389539,
                first_name: 'Fred',
                last_name: 'Rudd',
                user_sales: '57.00'
            }
        ],
        item_sales: [
            {
                item_id: 276614,
                item_name: 'Turkey and Swiss',
                item_sales: '1239.45'
            },
            {
                item_id: 276612,
                item_name: 'Italian Sandwich',
                item_sales: '793.75'
            },
            {
                item_id: 276633,
                item_name: 'Potato Chips',
                item_sales: '733.10'
            },
            {
                item_id: 276613,
                item_name: 'Roast Beef and Provolone',
                item_sales: '542.35'
            },
            {
                item_id: 276626,
                item_name: 'Cobb Salad',
                item_sales: '396.00'
            }
        ],
        device_sales: {
            labels: [
                'android',
                'iphone',
                'web',
                'web-mobile'
            ],
            sales: [
                '207.85',
                '757.75',
                '1263.40',
                '106.20'
            ]
        },
        returning_new: {
            labels: [
                'Returning',
                'New Customer'
            ],
            chart_values: [
                118,
                27
            ]
        },
        day_of_week: {
            labels: [
                'Sunday',
                'Monday',
                'Tuesday',
                'Wednesday',
                'Thursday',
                'Friday',
                'Saturday'
            ],
            sales: [
                '288.20',
                '333.35',
                '455.50',
                '366.80',
                '255.45',
                '321.70',
                '400.25'
            ]
        }
    };

    service.demo_data.current_year = {
        labels: [
            'Jan',
            'Feb',
            'Mar',
            'Apr',
            'May',
            'Jun',
            'Jul',
            'Aug'
        ],
        chart_values: [
            [
                '25121.30',
                '25798.50',
                '28707.50',
                '25971.75',
                '29790.40',
                '28098.95',
                '27178.65',
                '3021.45'
            ],
            [
                1666,
                1506,
                1776,
                1584,
                1826,
                1737,
                1658,
                200
            ]
        ],
        user_sales: [
            {
                user_id: 5996271,
                first_name: 'Jess',
                last_name: 'Gerthe',
                user_sales: '856.25'
            },
            {
                user_id: 6433287,
                first_name: 'Diane',
                last_name: 'Radley',
                user_sales: '597.35'
            },
            {
                user_id: 498704,
                first_name: 'Jonathan ',
                last_name: 'Levy',
                user_sales: '563.80'
            },
            {
                user_id: 121112,
                first_name: 'Rebecca',
                last_name: 'Taillon',
                user_sales: '562.30'
            },
            {
                user_id: 943814,
                first_name: 'dennis',
                last_name: 'nicks',
                user_sales: '561.20'
            }
        ],
        item_sales: [
            {
                item_id: 276614,
                item_name: 'Turkey and Swiss',
                item_sales: '98665.95'
            },
            {
                item_id: 276612,
                item_name: 'Italian Sandwich',
                item_sales: '91375.15'
            },
            {
                item_id: 276648,
                item_name: 'Cookies',
                item_sales: '46783.90'
            },
            {
                item_id: 276623,
                item_name: 'The Vegetarian',
                item_sales: '36539.10'
            },
            {
                item_id: 276613,
                item_name: 'Roast Beef and Provolone',
                item_sales: '32776.50'
            }
        ],
        device_sales: {
            labels: [
                'android',
                'iphone',
                'web',
                'web-mobile'
            ],
            sales: [
                '23239.45',
                '62668.60',
                '99614.15',
                '7480.05'
            ]
        },
        returning_new: {
            labels: [
                'Returning',
                'New Customer'
            ],
            chart_values: [
                3052,
                1249
            ]
        },
        day_of_week: {
            labels: [
                'Sunday',
                'Monday',
                'Tuesday',
                'Wednesday',
                'Thursday',
                'Friday',
                'Saturday'
            ],
            sales: [
                '12456.20',
                '31734.35',
                '31943.50',
                '32515.80',
                '35460.45',
                '33259.70',
                '15632.25'
            ]
        }
    };

    return service;
});
