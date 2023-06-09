<?php

return [
    'dict_type' => [
        [
            "dict_id"    => "1",
            "dict_name"  => "Gender",
            "dict_type"  => "sys_user_sex",
            "remark"     => "User Gender List",
            "status"     => "1",
            "created_at" => "2020-6-10 00:00:00",
            "updated_at" => "2020-6-10 00:00:00"
        ],
        [
            "dict_id"    => "2",
            "dict_name"  => "User status",
            "dict_type"  => "sys_user_status",
            "remark"     => "List of user status",
            "status"     => "1",
            "created_at" => "2020-6-10 00:00:00",
            "updated_at" => "2020-6-10 00:00:00"
        ],
        [
            "dict_id"    => "3",
            "dict_name"  => "Permissions status",
            "dict_type"  => "sys_permission_status",
            "remark"     => "Permanent status list",
            "status"     => "1",
            "created_at" => "2020-6-10 00:00:00",
            "updated_at" => "2020-6-10 00:00:00"
        ],
        [
            "dict_id"    => "4",
            "dict_name"  => "Permissions hidden",
            "dict_type"  => "sys_permission_hidden",
            "remark"     => "Whether the permission is hidden",
            "status"     => "1",
            "created_at" => "2020-6-10 00:00:00",
            "updated_at" => "2020-6-10 00:00:00"
        ],
        [
            "dict_id"    => "5",
            "dict_name"  => "Permissions",
            "dict_type"  => "sys_permission_type",
            "remark"     => "Permit type list",
            "status"     => "1",
            "created_at" => "2020-6-10 00:00:00",
            "updated_at" => "2020-6-10 00:00:00"
        ],
        [
            "dict_id"    => "6",
            "dict_name"  => "System suggestion type",
            "dict_type"  => "sys_advice_type",
            "remark"     => "System suggestion type",
            "status"     => "1",
            "created_at" => "2020-6-10 00:00:00",
            "updated_at" => "2020-6-10 00:00:00"
        ],
        [
            "dict_id"    => "7",
            "dict_name"  => "System suggestion status",
            "dict_type"  => "sys_advice_status",
            "remark"     => "System suggestion status",
            "status"     => "1",
            "created_at" => "2020-6-10 00:00:00",
            "updated_at" => "2020-6-10 00:00:00"
        ],
        [
            "dict_id"    => "8",
            "dict_name"  => "Notification management status",
            "dict_type"  => "sys_notice_status",
            "remark"     => "The status of notification management",
            "status"     => "1",
            "created_at" => "2020-6-10 00:00:00",
            "updated_at" => "2020-6-10 00:00:00"
        ],
        [
            "dict_id"    => "9",
            "dict_name"  => "Album status",
            "dict_type"  => "blog_album_status",
            "remark"     => "Starting status of the album",
            "status"     => "1",
            "created_at" => "2020-6-10 00:00:00",
            "updated_at" => "2020-6-10 00:00:00"
        ],
        [
            "dict_id"    => "10",
            "dict_name"  => "Album type",
            "dict_type"  => "blog_album_type",
            "remark"     => "Epolubes of albums",
            "status"     => "1",
            "created_at" => "2020-6-10 00:00:00",
            "updated_at" => "2020-6-10 00:00:00"
        ],
        [
            "dict_id"    => "12",
            "dict_name"  => "Timing task status",
            "dict_type"  => "sys_timed_task_status",
            "remark"     => "The status of time mission enumeration",
            "status"     => "1",
            "created_at" => "2020-6-10 00:00:00",
            "updated_at" => "2020-6-10 00:00:00"
        ],
        [
            "dict_id"    => "13",
            "dict_name"  => "Whether the permission is external",
            "dict_type"  => "sys_permission_is_link",
            "remark"     => "Whether the menu is external",
            "status"     => "1",
            "created_at" => "2021-6-15 11:47:07",
            "updated_at" => "2021-6-15 11:47:07"
        ],
        [
            "dict_id"    => "14",
            "dict_name"  => "Parameter setting type enumeration",
            "dict_type"  => "sys_global_config_type",
            "remark"     => "Parameter Set the type of module related enumeration",
            "status"     => "1",
            "created_at" => "2021-6-17 10:29:41",
            "updated_at" => "2021-6-17 10:29:41"
        ],
        [
            "dict_id"    => "15",
            "dict_name"  => "UP main timing statistical switch status",
            "dict_type"  => "lab_up_user_time_status",
            "remark"     => "Bilibili Assistant UP main time statistical switch enumeration",
            "status"     => "1",
            "created_at" => "2021-08-20 16:02:13",
            "updated_at" => "2021-08-20 16:02:13"
        ],
        [
            "dict_id"    => "16",
            "dict_name"  => "Video timing statistical switch status",
            "dict_type"  => "lab_video_time_status",
            "remark"     => "Bilibili Video timing statistical switch status",
            "status"     => "1",
            "created_at" => "2021-08-20 16:02:13",
            "updated_at" => "2021-08-20 16:02:13"
        ]


    ],
    'dict_data' => [

        [
            "dict_code"  => "1",
            "dict_sort"  => "1",
            "dict_label" => "Male",
            "dict_value" => "0",
            "dict_type"  => "sys_user_sex",
            "css_class"  => "",
            "list_class" => "",
            "is_default" => "1",
            "status"     => "1",
            "remark"     => "Sex: Male",
            "created_at" => "2020-6-10 00:00:00",
            "updated_at" => "2020-6-10 00:00:00"
        ],
        [
            "dict_code"  => "2",
            "dict_sort"  => "1",
            "dict_label" => "Female",
            "dict_value" => "1",
            "dict_type"  => "sys_user_sex",
            "css_class"  => "",
            "list_class" => "",
            "is_default" => "1",
            "status"     => "1",
            "remark"     => "Gender: Female",
            "created_at" => "2020-6-10 00:00:00",
            "updated_at" => "2020-6-10 00:00:00"
        ],
        [
            "dict_code"  => "3",
            "dict_sort"  => "1",
            "dict_label" => "Other",
            "dict_value" => "2",
            "dict_type"  => "sys_user_sex",
            "css_class"  => "",
            "list_class" => "",
            "is_default" => "1",
            "status"     => "1",
            "remark"     => "Other gender",
            "created_at" => "2020-6-10 00:00:00",
            "updated_at" => "2020-6-10 00:00:00"
        ],
        [
            "dict_code"  => "4",
            "dict_sort"  => "1",
            "dict_label" => "Enable",
            "dict_value" => "1",
            "dict_type"  => "sys_user_status",
            "css_class"  => "",
            "list_class" => "",
            "is_default" => "1",
            "status"     => "1",
            "remark"     => "User startup status",
            "created_at" => "2020-6-10 00:00:00",
            "updated_at" => "2020-6-10 00:00:00"
        ],
        [
            "dict_code"  => "5",
            "dict_sort"  => "1",
            "dict_label" => "Disable",
            "dict_value" => "0",
            "dict_type"  => "sys_user_status",
            "css_class"  => "",
            "list_class" => "",
            "is_default" => "1",
            "status"     => "1",
            "remark"     => "User disable status",
            "created_at" => "2020-6-10 00:00:00",
            "updated_at" => "2020-6-10 00:00:00"
        ],
        [
            "dict_code"  => "6",
            "dict_sort"  => "1",
            "dict_label" => "Enable",
            "dict_value" => "1",
            "dict_type"  => "sys_permission_status",
            "css_class"  => "",
            "list_class" => "",
            "is_default" => "1",
            "status"     => "1",
            "remark"     => "Permissions enable status",
            "created_at" => "2020-6-10 00:00:00",
            "updated_at" => "2020-6-10 00:00:00"
        ],
        [
            "dict_code"  => "7",
            "dict_sort"  => "1",
            "dict_label" => "Disable",
            "dict_value" => "0",
            "dict_type"  => "sys_permission_status",
            "css_class"  => "",
            "list_class" => "",
            "is_default" => "1",
            "status"     => "1",
            "remark"     => "Permissions disable status",
            "created_at" => "2020-6-10 00:00:00",
            "updated_at" => "2020-6-10 00:00:00"
        ],
        [
            "dict_code"  => "8",
            "dict_sort"  => "1",
            "dict_label" => "Yes",
            "dict_value" => "1",
            "dict_type"  => "sys_permission_hidden",
            "css_class"  => "",
            "list_class" => "",
            "is_default" => "1",
            "status"     => "1",
            "remark"     => "Hidden authority",
            "created_at" => "2020-6-10 00:00:00",
            "updated_at" => "2020-6-10 00:00:00"
        ],
        [
            "dict_code"  => "9",
            "dict_sort"  => "1",
            "dict_label" => "No",
            "dict_value" => "0",
            "dict_type"  => "sys_permission_hidden",
            "css_class"  => "",
            "list_class" => "",
            "is_default" => "1",
            "status"     => "1",
            "remark"     => "Non -hidden permissions",
            "created_at" => "2020-6-10 00:00:00",
            "updated_at" => "2020-6-10 00:00:00"
        ],
        [
            "dict_code"  => "10",
            "dict_sort"  => "1",
            "dict_label" => "Menu",
            "dict_value" => "1",
            "dict_type"  => "sys_permission_type",
            "css_class"  => "",
            "list_class" => "",
            "is_default" => "1",
            "status"     => "1",
            "remark"     => "Menu permissions type",
            "created_at" => "2020-6-10 00:00:00",
            "updated_at" => "2020-6-10 00:00:00"
        ],
        [
            "dict_code"  => "11",
            "dict_sort"  => "1",
            "dict_label" => "Button",
            "dict_value" => "2",
            "dict_type"  => "sys_permission_type",
            "css_class"  => "",
            "list_class" => "",
            "is_default" => "1",
            "status"     => "1",
            "remark"     => "Button permissions type",
            "created_at" => "2020-6-10 00:00:00",
            "updated_at" => "2020-6-10 00:00:00"
        ],
        [
            "dict_code"  => "12",
            "dict_sort"  => "1",
            "dict_label" => "Interface",
            "dict_value" => "3",
            "dict_type"  => "sys_permission_type",
            "css_class"  => "",
            "list_class" => "",
            "is_default" => "1",
            "status"     => "1",
            "remark"     => "Interface permissions",
            "created_at" => "2020-6-10 00:00:00",
            "updated_at" => "2020-6-10 00:00:00"
        ],
        [
            "dict_code"  => "13",
            "dict_sort"  => "1",
            "dict_label" => "Bug",
            "dict_value" => "0",
            "dict_type"  => "sys_advice_type",
            "css_class"  => "",
            "list_class" => "",
            "is_default" => "1",
            "status"     => "1",
            "remark"     => "bug",
            "created_at" => "2020-6-10 00:00:00",
            "updated_at" => "2020-6-10 00:00:00"
        ],
        [
            "dict_code"  => "14",
            "dict_sort"  => "1",
            "dict_label" => "Optimization",
            "dict_value" => "1",
            "dict_type"  => "sys_advice_type",
            "css_class"  => "",
            "list_class" => "",
            "is_default" => "1",
            "status"     => "1",
            "remark"     => "Optimization",
            "created_at" => "2020-6-10 00:00:00",
            "updated_at" => "2020-6-10 00:00:00"
        ],
        [
            "dict_code"  => "15",
            "dict_sort"  => "1",
            "dict_label" => "Mix",
            "dict_value" => "2",
            "dict_type"  => "sys_advice_type",
            "css_class"  => "",
            "list_class" => "",
            "is_default" => "1",
            "status"     => "1",
            "remark"     => "Mixed type",
            "created_at" => "2020-6-10 00:00:00",
            "updated_at" => "2020-6-10 00:00:00"
        ],
        [
            "dict_code"  => "16",
            "dict_sort"  => "1",
            "dict_label" => "To be solved",
            "dict_value" => "0",
            "dict_type"  => "sys_advice_status",
            "css_class"  => "",
            "list_class" => "",
            "is_default" => "1",
            "status"     => "1",
            "remark"     => "To be resolved",
            "created_at" => "2020-6-10 00:00:00",
            "updated_at" => "2020-6-10 00:00:00"
        ],
        [
            "dict_code"  => "17",
            "dict_sort"  => "1",
            "dict_label" => "Solved",
            "dict_value" => "1",
            "dict_type"  => "sys_advice_status",
            "css_class"  => "",
            "list_class" => "",
            "is_default" => "1",
            "status"     => "1",
            "remark"     => "Solved status",
            "created_at" => "2020-6-10 00:00:00",
            "updated_at" => "2020-6-10 00:00:00"
        ],
        [
            "dict_code"  => "18",
            "dict_sort"  => "1",
            "dict_label" => "Closed",
            "dict_value" => "2",
            "dict_type"  => "sys_advice_status",
            "css_class"  => "",
            "list_class" => "",
            "is_default" => "1",
            "status"     => "1",
            "remark"     => "Disabled",
            "created_at" => "2020-6-10 00:00:00",
            "updated_at" => "2020-6-10 00:00:00"
        ],
        [
            "dict_code"  => "19",
            "dict_sort"  => "1",
            "dict_label" => "Unpublished",
            "dict_value" => "0",
            "dict_type"  => "sys_notice_status",
            "css_class"  => "",
            "list_class" => "",
            "is_default" => "1",
            "status"     => "1",
            "remark"     => "Unpublished status",
            "created_at" => "2020-6-10 00:00:00",
            "updated_at" => "2020-6-10 00:00:00"
        ],
        [
            "dict_code"  => "20",
            "dict_sort"  => "1",
            "dict_label" => "Published",
            "dict_value" => "1",
            "dict_type"  => "sys_notice_status",
            "css_class"  => "",
            "list_class" => "",
            "is_default" => "1",
            "status"     => "1",
            "remark"     => "Published status",
            "created_at" => "2020-6-10 00:00:00",
            "updated_at" => "2020-6-10 00:00:00"
        ],
        [
            "dict_code"  => "21",
            "dict_sort"  => "1",
            "dict_label" => "Enable",
            "dict_value" => "1",
            "dict_type"  => "blog_album_status",
            "css_class"  => "",
            "list_class" => "",
            "is_default" => "1",
            "status"     => "1",
            "remark"     => "Album startup status",
            "created_at" => "2020-6-10 00:00:00",
            "updated_at" => "2020-6-10 00:00:00"
        ],
        [
            "dict_code"  => "22",
            "dict_sort"  => "2",
            "dict_label" => "Disable",
            "dict_value" => "0",
            "dict_type"  => "blog_album_status",
            "css_class"  => "",
            "list_class" => "",
            "is_default" => "1",
            "status"     => "1",
            "remark"     => "Album disable status",
            "created_at" => "2020-6-10 00:00:00",
            "updated_at" => "2020-6-10 00:00:00"
        ],
        [
            "dict_code"  => "23",
            "dict_sort"  => "1",
            "dict_label" => "General album",
            "dict_value" => "1",
            "dict_type"  => "blog_album_type",
            "css_class"  => "",
            "list_class" => "",
            "is_default" => "1",
            "status"     => "1",
            "remark"     => "General album",
            "created_at" => "2020-6-10 00:00:00",
            "updated_at" => "2020-6-10 00:00:00"
        ],
        [
            "dict_code"  => "24",
            "dict_sort"  => "2",
            "dict_label" => "Album",
            "dict_value" => "2",
            "dict_type"  => "blog_album_type",
            "css_class"  => "",
            "list_class" => "",
            "is_default" => "1",
            "status"     => "1",
            "remark"     => "Album",
            "created_at" => "2020-6-10 00:00:00",
            "updated_at" => "2020-6-10 00:00:00"
        ],
        [
            "dict_code"  => "25",
            "dict_sort"  => "1",
            "dict_label" => "Enable",
            "dict_value" => "1",
            "dict_type"  => "sys_timed_task_status",
            "css_class"  => "",
            "list_class" => "",
            "is_default" => "1",
            "status"     => "1",
            "remark"     => "Enable status",
            "created_at" => "2020-6-10 00:00:00",
            "updated_at" => "2020-6-10 00:00:00"
        ],
        [
            "dict_code"  => "26",
            "dict_sort"  => "1",
            "dict_label" => "Disable",
            "dict_value" => "0",
            "dict_type"  => "sys_timed_task_status",
            "css_class"  => "",
            "list_class" => "",
            "is_default" => "1",
            "status"     => "1",
            "remark"     => "Disable",
            "created_at" => "2020-6-10 00:00:00",
            "updated_at" => "2020-6-10 00:00:00"
        ],
        [
            "dict_code"  => "27",
            "dict_sort"  => "1",
            "dict_label" => "Yes",
            "dict_value" => "1",
            "dict_type"  => "sys_permission_is_link",
            "css_class"  => "",
            "list_class" => "",
            "is_default" => "1",
            "status"     => "1",
            "remark"     => "Is an external chain",
            "created_at" => "2021-6-15 11:47:19",
            "updated_at" => "2021-6-15 11:47:19"
        ],
        [
            "dict_code"  => "28",
            "dict_sort"  => "1",
            "dict_label" => "no",
            "dict_value" => "0",
            "dict_type"  => "sys_permission_is_link",
            "css_class"  => "",
            "list_class" => "",
            "is_default" => "1",
            "status"     => "1",
            "remark"     => "Not the external chain",
            "created_at" => "2021-6-15 11:47:31",
            "updated_at" => "2021-6-15 11:47:31"
        ],
        [
            "dict_code"  => "29",
            "dict_sort"  => "1",
            "dict_label" => "Text",
            "dict_value" => "text",
            "dict_type"  => "sys_global_config_type",
            "css_class"  => "",
            "list_class" => "",
            "is_default" => "1",
            "status"     => "1",
            "remark"     => "Text(string, int)",
            "created_at" => "2021-6-17 10:30:03",
            "updated_at" => "2021-6-17 10:30:03"
        ],
        [
            "dict_code"  => "30",
            "dict_sort"  => "1",
            "dict_label" => "Boolean value",
            "dict_value" => "boolean",
            "dict_type"  => "sys_global_config_type",
            "css_class"  => "",
            "list_class" => "",
            "is_default" => "1",
            "status"     => "1",
            "remark"     => "Boolean value",
            "created_at" => "2021-6-17 10:30:29",
            "updated_at" => "2021-6-17 10:30:29"
        ],
        [
            "dict_code"  => "31",
            "dict_sort"  => "1",
            "dict_label" => "HTML",
            "dict_value" => "html",
            "dict_type"  => "sys_global_config_type",
            "css_class"  => "",
            "list_class" => "",
            "is_default" => "1",
            "status"     => "1",
            "remark"     => "HTML format",
            "created_at" => "2021-6-17 10:30:45",
            "updated_at" => "2021-6-17 10:30:45"
        ],
        [
            "dict_code"  => "32",
            "dict_sort"  => "1",
            "dict_label" => "JSON",
            "dict_value" => "json",
            "dict_type"  => "sys_global_config_type",
            "css_class"  => "",
            "list_class" => "",
            "is_default" => "1",
            "status"     => "1",
            "remark"     => "json format",
            "created_at" => "2021-08-24 15:37:09",
            "updated_at" => "2021-08-24 15:37:09"
        ],
        [
            "dict_code"  => "33",
            "dict_sort"  => "1",
            "dict_label" => "Open",
            "dict_value" => "1",
            "dict_type"  => "lab_up_user_time_status",
            "css_class"  => "",
            "list_class" => "",
            "is_default" => "1",
            "status"     => "1",
            "remark"     => "Open status",
            "created_at" => "2021-08-24 15:37:09",
            "updated_at" => "2021-08-24 15:37:09"
        ],
        [
            "dict_code"  => "34",
            "dict_sort"  => "1",
            "dict_label" => "Closed",
            "dict_value" => "0",
            "dict_type"  => "lab_up_user_time_status",
            "css_class"  => "",
            "list_class" => "",
            "is_default" => "0",
            "status"     => "1",
            "remark"     => "Disabled",
            "created_at" => "2021-08-24 15:37:09",
            "updated_at" => "2021-08-24 15:37:09"
        ],
        [
            "dict_code"  => "35",
            "dict_sort"  => "1",
            "dict_label" => "Open",
            "dict_value" => "1",
            "dict_type"  => "lab_video_time_status",
            "css_class"  => "",
            "list_class" => "",
            "is_default" => "1",
            "status"     => "1",
            "remark"     => "Open status",
            "created_at" => "2021-08-24 15:37:09",
            "updated_at" => "2021-08-24 15:37:09"
        ],
        [
            "dict_code"  => "36",
            "dict_sort"  => "1",
            "dict_label" => "Closed",
            "dict_value" => "0",
            "dict_type"  => "lab_video_time_status",
            "css_class"  => "",
            "list_class" => "",
            "is_default" => "0",
            "status"     => "1",
            "remark"     => "Disabled",
            "created_at" => "2021-08-24 15:37:09",
            "updated_at" => "2021-08-24 15:37:09"
        ],

    ],
];