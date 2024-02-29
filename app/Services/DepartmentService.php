<?php

namespace App\Services;

/**
 * DepartmentService class to manage department data.
 */
class DepartmentService
{

    /**
     * Departments array to hold department data.
     *
     * @var array
     */
    private $departments = [];

    /**
     * Constructor to initialize the departments array.
     */
    public function __construct()
    {
        // Replace the below line with your JSON string
        $jsonStr = '[
                          {
                            "id": "1",
                            "pcc_code": "ab",
                            "pcc_name": "Auto Collision Repair Technology",
                            "pcc_guideurl": "https://guides.pcc.edu/auto-collision-repair",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "2",
                            "pcc_code": "abe",
                            "pcc_name": "Adult Basic Education",
                            "pcc_guideurl": "https://guides.pcc.edu/GED",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "3",
                            "pcc_code": "ad",
                            "pcc_name": "Addiction Counseling",
                            "pcc_guideurl": "https://guides.pcc.edu/AddictionCounseling",
                            "last_updated": "2022-07-06 14:11:53"
                          },
                          {
                            "id": "4",
                            "pcc_code": "alc",
                            "pcc_name": "Alternative Learning Courses",
                            "pcc_guideurl": "https://guides.pcc.edu",
                            "last_updated": "2021-10-07 21:56:14"
                          },
                          {
                            "id": "5",
                            "pcc_code": "alm",
                            "pcc_name": "Alternative Learning Math",
                            "pcc_guideurl": "https://guides.pcc.edu",
                            "last_updated": "2021-10-07 21:56:19"
                          },
                          {
                            "id": "6",
                            "pcc_code": "am",
                            "pcc_name": "Automotive Service Technology",
                            "pcc_guideurl": "https://guides.pcc.edu/automotiveservice",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "7",
                            "pcc_code": "amt",
                            "pcc_name": "Aviation Maintenance Technology",
                            "pcc_guideurl": "https://guides.pcc.edu/aviation-maintenance",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "8",
                            "pcc_code": "apr",
                            "pcc_name": "Apprenticeship",
                            "pcc_guideurl": "https://guides.pcc.edu",
                            "last_updated": "2021-10-07 21:56:22"
                          },
                          {
                            "id": "9",
                            "pcc_code": "arch",
                            "pcc_name": "Architectural Design and Drafting",
                            "pcc_guideurl": "https://guides.pcc.edu/architecturaldesign",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "10",
                            "pcc_code": "art",
                            "pcc_name": "Art",
                            "pcc_guideurl": "https://guides.pcc.edu/art",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "11",
                            "pcc_code": "asl",
                            "pcc_name": "American Sign Language",
                            "pcc_guideurl": "https://guides.pcc.edu",
                            "last_updated": "2021-10-07 21:56:25"
                          },
                          {
                            "id": "12",
                            "pcc_code": "ath",
                            "pcc_name": "Anthropology",
                            "pcc_guideurl": "https://guides.pcc.edu/anthropology",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "13",
                            "pcc_code": "avs",
                            "pcc_name": "Aviation Science ",
                            "pcc_guideurl": "https://guides.pcc.edu/aviation-science",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "14",
                            "pcc_code": "ba",
                            "pcc_name": "Business Administration ",
                            "pcc_guideurl": "https://guides.pcc.edu/business",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "15",
                            "pcc_code": "bct",
                            "pcc_name": "Building Construction Technology",
                            "pcc_guideurl": "https://guides.pcc.edu/building-construction-tech",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "16",
                            "pcc_code": "bi",
                            "pcc_name": "Biology",
                            "pcc_guideurl": "https://guides.pcc.edu/biology",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "17",
                            "pcc_code": "bit",
                            "pcc_name": "Bioscience Technology",
                            "pcc_guideurl": "https://guides.pcc.edu/bioscience-tech",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "18",
                            "pcc_code": "cadd",
                            "pcc_name": "Computer Aided Design & Draft",
                            "pcc_guideurl": "https://guides.pcc.edu/CADD",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "19",
                            "pcc_code": "cas",
                            "pcc_name": "Computer Applications Systems",
                            "pcc_guideurl": "https://guides.pcc.edu/computer-office-systems",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "20",
                            "pcc_code": "ccet",
                            "pcc_name": "Civil and Construction Engineering Technology",
                            "pcc_guideurl": "https://guides.pcc.edu",
                            "last_updated": "2021-10-07 21:56:28"
                          },
                          {
                            "id": "21",
                            "pcc_code": "ceu",
                            "pcc_name": "Continuing Education Units",
                            "pcc_guideurl": "https://guides.pcc.edu",
                            "last_updated": "2021-10-07 21:56:31"
                          },
                          {
                            "id": "22",
                            "pcc_code": "cg",
                            "pcc_name": "College Success and Career Guidance",
                            "pcc_guideurl": "https://guides.pcc.edu/career-guidance",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "23",
                            "pcc_code": "ch",
                            "pcc_name": "Chemistry",
                            "pcc_guideurl": "https://guides.pcc.edu/chemistry",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "24",
                            "pcc_code": "chla",
                            "pcc_name": "Chicano/Latino Studies",
                            "pcc_guideurl": "https://guides.pcc.edu/chicano-latino-studies",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "25",
                            "pcc_code": "chn",
                            "pcc_name": "Chinese",
                            "pcc_guideurl": "https://guides.pcc.edu/chinese",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "26",
                            "pcc_code": "cis",
                            "pcc_name": "Computer Info Systems",
                            "pcc_guideurl": "https://guides.pcc.edu/computer-info-systems",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "27",
                            "pcc_code": "cja",
                            "pcc_name": "Criminal Justice",
                            "pcc_guideurl": "https://guides.pcc.edu/criminal-justice",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "28",
                            "pcc_code": "cmet",
                            "pcc_name": "Civil and Mechanical Engineering Technology",
                            "pcc_guideurl": "https://guides.pcc.edu/engineering",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "29",
                            "pcc_code": "comm",
                            "pcc_name": "Communication Studies",
                            "pcc_guideurl": "https://guides.pcc.edu/COMM",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "30",
                            "pcc_code": "cs",
                            "pcc_name": "Computer Science",
                            "pcc_guideurl": "https://guides.pcc.edu/computer-science",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "31",
                            "pcc_code": "css",
                            "pcc_name": "Crop Soil Science",
                            "pcc_guideurl": "https://guides.pcc.edu/horticulture",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "32",
                            "pcc_code": "ctt",
                            "pcc_name": "Computed Tomography",
                            "pcc_guideurl": "https://guides.pcc.edu/radiography",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "33",
                            "pcc_code": "da",
                            "pcc_name": "Dental Assisting",
                            "pcc_guideurl": "https://guides.pcc.edu/dental",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "34",
                            "pcc_code": "de",
                            "pcc_name": "Developmental Education",
                            "pcc_guideurl": "https://guides.pcc.edu",
                            "last_updated": "2021-10-07 21:56:34"
                          },
                          {
                            "id": "35",
                            "pcc_code": "dh",
                            "pcc_name": "Dental Hygiene",
                            "pcc_guideurl": "https://guides.pcc.edu/dental",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "36",
                            "pcc_code": "ds",
                            "pcc_name": "Diesel Service Technology",
                            "pcc_guideurl": "https://guides.pcc.edu/diesel-service-tech",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "37",
                            "pcc_code": "dst",
                            "pcc_name": "Dealer Service Technology",
                            "pcc_guideurl": "https://guides.pcc.edu/diesel-service-tech",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "38",
                            "pcc_code": "dt",
                            "pcc_name": "Dental Laboratory Technology",
                            "pcc_guideurl": "https://guides.pcc.edu/dental",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "39",
                            "pcc_code": "ec",
                            "pcc_name": "Economics",
                            "pcc_guideurl": "https://guides.pcc.edu/economics",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "40",
                            "pcc_code": "ece",
                            "pcc_name": "Early Childhood Education",
                            "pcc_guideurl": "https://guides.pcc.edu/ece",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "41",
                            "pcc_code": "ed",
                            "pcc_name": "Education",
                            "pcc_guideurl": "https://guides.pcc.edu/paraeducation",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "42",
                            "pcc_code": "eet",
                            "pcc_name": "Electronic Engineering Technology",
                            "pcc_guideurl": "https://guides.pcc.edu/engineering",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "43",
                            "pcc_code": "elt",
                            "pcc_name": "Electrical Trades",
                            "pcc_guideurl": "https://guides.pcc.edu",
                            "last_updated": "2021-10-07 21:56:36"
                          },
                          {
                            "id": "44",
                            "pcc_code": "em",
                            "pcc_name": "Emergency Management",
                            "pcc_guideurl": "https://guides.pcc.edu",
                            "last_updated": "2021-10-07 21:56:39"
                          },
                          {
                            "id": "45",
                            "pcc_code": "ems",
                            "pcc_name": "Emergency Medical Services (EMS/EMT)",
                            "pcc_guideurl": "https://guides.pcc.edu/ems",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "46",
                            "pcc_code": "eng",
                            "pcc_name": "English",
                            "pcc_guideurl": "https://guides.pcc.edu/literature",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "47",
                            "pcc_code": "engr",
                            "pcc_name": "Engineering",
                            "pcc_guideurl": "https://guides.pcc.edu/engineering",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "48",
                            "pcc_code": "epg",
                            "pcc_name": "Electric Power Generation",
                            "pcc_guideurl": "https://guides.pcc.edu",
                            "last_updated": "2021-10-07 21:56:41"
                          },
                          {
                            "id": "49",
                            "pcc_code": "es",
                            "pcc_name": "Ethnic Studies",
                            "pcc_guideurl": "https://guides.pcc.edu",
                            "last_updated": "2021-10-07 21:56:44"
                          },
                          {
                            "id": "50",
                            "pcc_code": "esol",
                            "pcc_name": "English for Speakers of Other Languages",
                            "pcc_guideurl": "https://guides.pcc.edu/esol",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "51",
                            "pcc_code": "esr",
                            "pcc_name": "Environmental Studies",
                            "pcc_guideurl": "https://guides.pcc.edu/environment",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "52",
                            "pcc_code": "fmt",
                            "pcc_name": "Facilities Maintenance Technology",
                            "pcc_guideurl": "https://guides.pcc.edu/hvac",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "53",
                            "pcc_code": "fn",
                            "pcc_name": "Foods and Nutrition",
                            "pcc_guideurl": "https://guides.pcc.edu/foods-and-nutrition",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "54",
                            "pcc_code": "fp",
                            "pcc_name": "Fire Protection",
                            "pcc_guideurl": "https://guides.pcc.edu/fireprotection",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "55",
                            "pcc_code": "fr",
                            "pcc_name": "French",
                            "pcc_guideurl": "https://guides.pcc.edu/french",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "56",
                            "pcc_code": "ft",
                            "pcc_name": "Exercise Science",
                            "pcc_guideurl": "https://guides.pcc.edu/exercisescience",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "57",
                            "pcc_code": "g",
                            "pcc_name": "Geology",
                            "pcc_guideurl": "https://guides.pcc.edu/geology",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "58",
                            "pcc_code": "gd",
                            "pcc_name": "Graphic Design",
                            "pcc_guideurl": "https://guides.pcc.edu/graphicdesign",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "59",
                            "pcc_code": "ged",
                            "pcc_name": "General Education Development",
                            "pcc_guideurl": "https://guides.pcc.edu",
                            "last_updated": "2021-10-07 21:56:46"
                          },
                          {
                            "id": "60",
                            "pcc_code": "geo",
                            "pcc_name": "Geography",
                            "pcc_guideurl": "https://guides.pcc.edu/geography",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "61",
                            "pcc_code": "ger",
                            "pcc_name": "German",
                            "pcc_guideurl": "https://guides.pcc.edu/german",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "62",
                            "pcc_code": "grn",
                            "pcc_name": "Gerontology",
                            "pcc_guideurl": "https://guides.pcc.edu/gerontology",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "63",
                            "pcc_code": "gs",
                            "pcc_name": "General Science",
                            "pcc_guideurl": "https://guides.pcc.edu",
                            "last_updated": "2021-10-07 21:56:48"
                          },
                          {
                            "id": "64",
                            "pcc_code": "he",
                            "pcc_name": "Health Studies",
                            "pcc_guideurl": "https://guides.pcc.edu/healthstudies",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "65",
                            "pcc_code": "hec",
                            "pcc_name": "Parenting Education and Family Life",
                            "pcc_guideurl": "https://guides.pcc.edu/ece",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "66",
                            "pcc_code": "him",
                            "pcc_name": "Health Information Management",
                            "pcc_guideurl": "https://guides.pcc.edu/him",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "67",
                            "pcc_code": "hon",
                            "pcc_name": "Honors",
                            "pcc_guideurl": "https://guides.pcc.edu",
                            "last_updated": "2021-10-07 21:56:51"
                          },
                          {
                            "id": "68",
                            "pcc_code": "hor",
                            "pcc_name": "Horticulture",
                            "pcc_guideurl": "https://guides.pcc.edu/horticulture",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "69",
                            "pcc_code": "hr",
                            "pcc_name": "Culinary Assistant",
                            "pcc_guideurl": "https://guides.pcc.edu",
                            "last_updated": "2021-10-07 21:56:54"
                          },
                          {
                            "id": "70",
                            "pcc_code": "hst",
                            "pcc_name": "History",
                            "pcc_guideurl": "https://guides.pcc.edu/history",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "71",
                            "pcc_code": "hum",
                            "pcc_name": "Humanities",
                            "pcc_guideurl": "https://guides.pcc.edu/humanities",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "72",
                            "pcc_code": "hus",
                            "pcc_name": "Family and Human Services",
                            "pcc_guideurl": "https://guides.pcc.edu/hus",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "73",
                            "pcc_code": "id",
                            "pcc_name": "Interior Design",
                            "pcc_guideurl": "https://guides.pcc.edu/interiordesign",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "74",
                            "pcc_code": "ids",
                            "pcc_name": "Interdisciplinary Studies",
                            "pcc_guideurl": "https://guides.pcc.edu",
                            "last_updated": "2021-10-07 21:56:57"
                          },
                          {
                            "id": "75",
                            "pcc_code": "insp",
                            "pcc_name": "Building Inspections Technology",
                            "pcc_guideurl": "https://guides.pcc.edu",
                            "last_updated": "2021-10-07 21:56:59"
                          },
                          {
                            "id": "76",
                            "pcc_code": "intl",
                            "pcc_name": "International Studies",
                            "pcc_guideurl": "https://guides.pcc.edu/InternationalStudies",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "77",
                            "pcc_code": "irw",
                            "pcc_name": "Integrated Reading and Writing",
                            "pcc_guideurl": "https://guides.pcc.edu/reading",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "78",
                            "pcc_code": "itp",
                            "pcc_name": "ASL/English Interpreting",
                            "pcc_guideurl": "https://guides.pcc.edu/aslinterpretation",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "79",
                            "pcc_code": "j",
                            "pcc_name": "Journalism",
                            "pcc_guideurl": "https://guides.pcc.edu/journalism",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "80",
                            "pcc_code": "jpn",
                            "pcc_name": "Japanese",
                            "pcc_guideurl": "https://guides.pcc.edu/japanese",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "81",
                            "pcc_code": "lat",
                            "pcc_name": "Landscape Technology",
                            "pcc_guideurl": "https://guides.pcc.edu/landscape",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "82",
                            "pcc_code": "lib",
                            "pcc_name": "Library ",
                            "pcc_guideurl": "https://guides.pcc.edu/lib101",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "83",
                            "pcc_code": "ma",
                            "pcc_name": "Medical Assisting ",
                            "pcc_guideurl": "https://guides.pcc.edu/medical-assistant",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "84",
                            "pcc_code": "mch",
                            "pcc_name": "Machine Manufacturing Technology",
                            "pcc_guideurl": "https://guides.pcc.edu/machinemanufacturing",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "85",
                            "pcc_code": "mlt",
                            "pcc_name": "Medical Lab Technology",
                            "pcc_guideurl": "https://guides.pcc.edu/medical-lab-tech",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "86",
                            "pcc_code": "mm",
                            "pcc_name": "Multimedia",
                            "pcc_guideurl": "https://guides.pcc.edu/multimedia",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "87",
                            "pcc_code": "mp",
                            "pcc_name": "Medical Professions",
                            "pcc_guideurl": "https://guides.pcc.edu/healthprofessionals",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "88",
                            "pcc_code": "mri",
                            "pcc_name": "Magnetic Resonance Imaging",
                            "pcc_guideurl": "https://guides.pcc.edu/radiography",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "89",
                            "pcc_code": "msd",
                            "pcc_name": "Management and Supervisory Development",
                            "pcc_guideurl": "https://guides.pcc.edu/management",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "90",
                            "pcc_code": "mt",
                            "pcc_name": "Microelectronics Technology",
                            "pcc_guideurl": "https://guides.pcc.edu/microelectronics-tech",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "91",
                            "pcc_code": "mth",
                            "pcc_name": "Math",
                            "pcc_guideurl": "https://guides.pcc.edu/math",
                            "last_updated": "2024-01-26 15:39:17"
                          },
                          {
                            "id": "92",
                            "pcc_code": "muc",
                            "pcc_name": "Music & Sonic Arts",
                            "pcc_guideurl": "https://guides.pcc.edu/music",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "93",
                            "pcc_code": "mup",
                            "pcc_name": "Applied Music",
                            "pcc_guideurl": "https://guides.pcc.edu/music",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "94",
                            "pcc_code": "mus",
                            "pcc_name": "Music",
                            "pcc_guideurl": "https://guides.pcc.edu/music",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "95",
                            "pcc_code": "nas",
                            "pcc_name": "Native American Studies",
                            "pcc_guideurl": "https://guides.pcc.edu/NAS",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "96",
                            "pcc_code": "nrs",
                            "pcc_name": "Nursing",
                            "pcc_guideurl": "https://guides.pcc.edu/nursing",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "97",
                            "pcc_code": "omt",
                            "pcc_name": "Ophthalmic Medical Technology",
                            "pcc_guideurl": "https://guides.pcc.edu/omt",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "98",
                            "pcc_code": "os",
                            "pcc_name": "Office Systems",
                            "pcc_guideurl": "https://guides.pcc.edu/computer-office-systems",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "99",
                            "pcc_code": "ost",
                            "pcc_name": "Occupational Skills Training",
                            "pcc_guideurl": "https://guides.pcc.edu",
                            "last_updated": "2021-10-07 21:57:02"
                          },
                          {
                            "id": "100",
                            "pcc_code": "pe",
                            "pcc_name": "Physical Education",
                            "pcc_guideurl": "https://guides.pcc.edu/pe",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "101",
                            "pcc_code": "phl",
                            "pcc_name": "Philosophy",
                            "pcc_guideurl": "https://guides.pcc.edu/philosophy",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "102",
                            "pcc_code": "phy",
                            "pcc_name": "Physics",
                            "pcc_guideurl": "https://guides.pcc.edu/physics",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "103",
                            "pcc_code": "pl",
                            "pcc_name": "Paralegal",
                            "pcc_guideurl": "https://guides.pcc.edu/paralegal",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "104",
                            "pcc_code": "ps",
                            "pcc_name": "Political Science",
                            "pcc_guideurl": "https://guides.pcc.edu/political-science",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "105",
                            "pcc_code": "psy",
                            "pcc_name": "Psychology",
                            "pcc_guideurl": "https://guides.pcc.edu/psychology",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "106",
                            "pcc_code": "r",
                            "pcc_name": "Religious Studies",
                            "pcc_guideurl": "https://guides.pcc.edu/religion",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "107",
                            "pcc_code": "rad",
                            "pcc_name": "Radiography",
                            "pcc_guideurl": "https://guides.pcc.edu/radiography",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "108",
                            "pcc_code": "rd",
                            "pcc_name": "Reading",
                            "pcc_guideurl": "https://guides.pcc.edu/reading",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "109",
                            "pcc_code": "rus",
                            "pcc_name": "Russian Language",
                            "pcc_guideurl": "https://guides.pcc.edu/russian-language",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "110",
                            "pcc_code": "sc",
                            "pcc_name": "Skill Center",
                            "pcc_guideurl": "https://guides.pcc.edu",
                            "last_updated": "2021-10-07 21:57:05"
                          },
                          {
                            "id": "111",
                            "pcc_code": "sj",
                            "pcc_name": "Social Justice",
                            "pcc_guideurl": "https://guides.pcc.edu",
                            "last_updated": "2021-10-07 21:57:07"
                          },
                          {
                            "id": "112",
                            "pcc_code": "soc",
                            "pcc_name": "Sociology",
                            "pcc_guideurl": "https://guides.pcc.edu/sociology",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "113",
                            "pcc_code": "spa",
                            "pcc_name": "Spanish",
                            "pcc_guideurl": "https://guides.pcc.edu/spanish",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "121",
                            "pcc_code": "stat",
                            "pcc_name": "Statistics",
                            "pcc_guideurl": "https://guides.pcc.edu/statistics",
                            "last_updated": "2023-10-12 16:16:47"
                          },
                          {
                            "id": "114",
                            "pcc_code": "ta",
                            "pcc_name": "Theatre Arts",
                            "pcc_guideurl": "https://guides.pcc.edu/theatre",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "115",
                            "pcc_code": "uas",
                            "pcc_name": "Unoccupied Aircraft Systems",
                            "pcc_guideurl": "https://guides.pcc.edu",
                            "last_updated": "2021-10-07 21:57:10"
                          },
                          {
                            "id": "116",
                            "pcc_code": "vir",
                            "pcc_name": "Vascular Interventional Catheterization",
                            "pcc_guideurl": "https://guides.pcc.edu",
                            "last_updated": "2021-10-07 21:57:14"
                          },
                          {
                            "id": "117",
                            "pcc_code": "vt",
                            "pcc_name": "Veterinary Technology",
                            "pcc_guideurl": "https://guides.pcc.edu/vettech",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "118",
                            "pcc_code": "wld",
                            "pcc_name": "Welding",
                            "pcc_guideurl": "https://guides.pcc.edu/welding",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "119",
                            "pcc_code": "wr",
                            "pcc_name": "Writing",
                            "pcc_guideurl": "https://guides.pcc.edu/writing",
                            "last_updated": "0000-00-00 00:00:00"
                          },
                          {
                            "id": "120",
                            "pcc_code": "ws",
                            "pcc_name": "Women’s and Gender Studies",
                            "pcc_guideurl": "https://guides.pcc.edu/womens-studies",
                            "last_updated": "0000-00-00 00:00:00"
                          }
                        ]';
        $this->departments = json_decode($jsonStr, true);
    }

    /**
     * Get all departments in the required format.
     *
     * @return array An array of all departments in the format "pcc_code" => "PCC_CODE - pcc_name".
     */
    public function getAllDepartments(): array
    {
        $formattedDepartments = [];

        foreach ($this->departments as $department) {
            $formattedDepartments[$department['pcc_code']] = strtoupper($department['pcc_code']) . " - " . $department['pcc_name'];
        }

        return $formattedDepartments;
    }

    /**
     * Get a random department for testing in the required format.
     *
     * @return array
     */
    public function getRandomDepartment(): array
    {
        $randomDepartment = $this->getRandomDepartment(); // Assuming you have a method to get a random department

        return [
            $randomDepartment['pcc_code'] => strtoupper($randomDepartment['pcc_code']) . " - " . $randomDepartment['pcc_name']
        ];
    }

    /**
     * Find a department by its code.
     *
     * @param string $code The department code to search for.
     * @return array|null The department if found, or null if not found.
     */
    public function findByCode(string $code): ?array
    {
        foreach ($this->departments as $department) {
            if ($department['pcc_code'] === $code) {
                return [
                    $department['pcc_code'] => strtoupper($department['pcc_code']) . " - " . $department['pcc_name']
                ];
            }
        }

        return null;
    }
}
