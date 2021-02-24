<?php

if (!class_exists('QN_TFX_List_Options'))
{
    class QN_TFX_List_Options
    {

        public const TFX_CHRIS_PUTNAM = 10072759;
        public const TFX_KELLY_ZARCONE = 3488372;

        /**
            "key" => [
                "name" => "KEY - Name",
                "type" => "list",
                "id" => X,
                "location" => "top",
                "assign" => self::TFX_CHRIS_PUTNAM,
            ],
        */

        public static $options = [
            "ag" => [
                "name" => "AG - Annual Goals, Habits, etc",
                "type" => "list",
                "id" => 31927383,
                "location" => "top",
            ],
            "bp" => [
                "name" => "BP - Build / Redesign Process Project - Blocks & Core Vitals",
                "type" => "project",
                "id" => 14992659,
                "location" => "bottom",
            ],
            "cn" => [
                "name" => "CN - Coaching Notes",
                "type" => "project",
                "id" => 14461689,
                "location" => "bottom",
                "template" => "",
            ],
            "dn" => [
                "name" => "DN - DevNext - Bootcamp, CDT, PP, etc.",
                "type" => "list",
                "id" => 29965674,
                "location" => "bottom",
                "assign" => self::TFX_CHRIS_PUTNAM,
            ],
            "dp" => [
                "name" => "DP - Dev Project",
                "type" => "project",
                "id" => 11282705,
                "location" => "bottom",
            ],
            "em" => [
                "name" => "EM - Weekly E-mail",
                "type" => "list",
                "id" => 31944125,
                "location" => "bottom",
            ],
            "fxl" => [
                "name" => "FXL - FXLearns Suggestion",
                "type" => "list",
                "id" => 24862738,
                "location" => "bottom",
                "template" => "Chris P - title - link",
            ],
            "intpr" => [
                "name" => "INTPR - Interactive Project",
                "type" => "project",
                "id" => 14667798,
                "location" => "bottom",
            ],
            "intop" => [
                "name" => "INTOP - Interactive Operations Project",
                "type" => "project",
                "id" => 1394938,
                "location" => "bottom",
            ],
            "ip" => [
                "name" => "IP - IP Dev Queue",
                "type" => "list",
                "id" => 29118382,
                "location" => "bottom",
            ],
            "km" => [
                "name" => "KM - Kelly - Meeting Agenda",
                "type" => "list",
                "id" => 30906207,
                "location" => "bottom",
                "assign" => self::TFX_CHRIS_PUTNAM,
            ],
            "ks" => [
                "name" => "KS - Knowledge Sharing",
                "type" => "list",
                "id" => 29593658,
                "location" => "top",
                "assign" => self::TFX_CHRIS_PUTNAM,
            ],
            "kp" => [
                "name" => "KP - Kelly's Project / Lists, KZ, KLZ",
                "type" => "project",
                "id" => 12218350,
                "location" => "bottom",
                "assign" => self::TFX_KELLY_ZARCONE,
            ],
            "op" => [
                "name" => "OP - Ops List",
                "type" => "list",
                "id" => 30906209,
                "location" => "top",
                "assign" => self::TFX_CHRIS_PUTNAM,
                "template" => "[:;] <b>[;]</b> (A;|D;) ;",
            ],
            "pi" => [
                "name" => "PI - Process Improvements",
                "type" => "project",
                "id" => 11878515,
                "location" => "bottom",
                "template" => "<b>Added by ;</b> ;",
            ],
            "pto" => [
                "name" => "PTO - PTO/Off Item in Dev Queue",
                "type" => "list",
                "id" => 29595528,
                "location" => "bottom",
                "template" => "<b>On scheduled day</b> [OFF] Time Off - ",
            ],
            "sq" => [
                "name" => "SQ - Dev Queue",
                "type" => "list",
                "id" => 29595528,
                "location" => "bottom",
            ],
            "ti" => [
                "name" => "TI - Team Initiative",
                "type" => "list",
                "id" => 30906208,
                "location" => "bottom",
                "assign" => self::TFX_CHRIS_PUTNAM,
                "template" => "<b>[;high_medium_low]</b> ;",
            ],
        ];

    }
}

// Note: leave this for packaging ?>
