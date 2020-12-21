<?php
/**
 * quicknote
 */
Class Quicknote extends Console_Abstract
{
    const VERSION = "1.0.1";

    // Name of script and directory to store config
    const SHORTNAME = 'quicknote';

    /**
     * Callable Methods
     */
    protected static $METHODS = [
        'add',
        'add_as',
        'add_cl',
        'add_fx',
        'add_gh',
        'add_ml',
        'add_qn',
        'add_tx',
    ];

    protected $__quicknotes_file = "OK to run as root";
    public $quicknotes_file = "/tmp/quicknotes.md";

    protected $__ptfx_exec = "PTFX Exec";
    public $ptfx_exec = "/usr/local/bin/ptfx";

    public const TFX_CHRIS_PUTNAM = 10072759;
    public const TFX_KELLY_ZARCONE = 3488372;

    protected $___add = [
        "Add a new note - with interactive prompt for details"
    ];
	public function add()
    {
        $types = [
            "AS - Asana Todo" => "as",
            "CL - Calendar Event" => "cl",
            "FX - TFX List" => "fx",
            "GH - Github Issue" => "gh",
            "ML - E-mail Message (Email)" => "ml",
            "QN - Miscellaneous - Quicknotes file" => "qn",
            "TX - Text Message (TXT, SMS)" => "tx",
        ];
        $type_keys = array_keys($types);

        $type = $this->select($type_keys, "Select type of note", 5);

        $method = [$this, "add_" . $types[$type]];
        if (is_callable($method))
        {
            call_user_func($method);
        }
        else
        {
            throw new Exception("Invalid method: " . $method[1]);
        }
    }

    protected $___add_as = [
        "Add a new note - Asana Todo",
    ];
	public function add_as()
    {
        echo "Add a new note - Asana Todo - Not yet implemented";
    }

    protected $___add_cl = [
        "Add a new note - Calendar Event",
    ];
	public function add_cl()
    {
        echo "Add a new note - Asana Todo - Not yet implemented";
    }

    protected $___add_fx = [
        "Add a new note - TFX List",
    ];
	public function add_fx()
    {
        $this->clear();

        $options = [
            "DN - DevNext - Bootcamp, CDT, PP, etc." => "dn",
            "EM - Weekly E-mail" => "em",
            "KS - Knowledge Sharing" => "ks",
            "KZ - Kelly's List" => "kz",
            "OP - Ops List" => "op",
            "TI - Team Initiative" => "ti",
        ];

        $option_configs = [
            "dn" => [
                "type" => "list",
                "id" => 29965674,
                "location" => "bottom",
                "assign" => self::TFX_CHRIS_PUTNAM,
            ],
            "em" => [
                "type" => "list",
                "id" => 31944125,
                "location" => "bottom",
            ],
            "ks" => [
                "type" => "list",
                "id" => 29593658,
                "location" => "top",
                "assign" => self::TFX_CHRIS_PUTNAM,
            ],
            "kz" => [
                "type" => "project",
                "project" => 12218350,
                "location" => "bottom",
                "assign" => self::TFX_KELLY_ZARCONE,
            ],
            "op" => [
                "type" => "list",
                "id" => 30906209,
                "location" => "top",
                "assign" => self::TFX_CHRIS_PUTNAM,
                "template" => "[:;] <b>[;]</b> (A;|D;) ;",
            ],
            "ti" => [
                "type" => "list",
                "id" => 30906208,
                "location" => "bottom",
                "assign" => self::TFX_CHRIS_PUTNAM,
                "template" => "<b>[;high_medium_low]</b> ;",
            ],
        ];

        $option_keys = array_keys($options);

        $option = $this->select($option_keys, "Select area to add item", 4);
        $option_key = $options[$option];
        $option_config = $option_configs[$option_key];

        $id = $option_config["id"];
        $type = $option_config["type"];
        $template = empty($option_config["template"]) ? "" : $option_config["template"];
        $assign = empty($option_config["assign"]) ? "" : $option_config["assign"];
        $location = $option_config["location"];

        $command = $this->ptfx_exec . ' create_todo ' . 
            '"'.$id.'" ' . // list or project id
            '"" '. // message (will prompt)
            '"'.$assign.'" ' . // user to assing
            '"'.$location.'" ' . // where to creat - top or bottom of list
            '"'.$type.'" ' . // type of id - list or project
            '"'.$template.'" ' . // message template
        '';

        $this->clear();
        $this->output("Creating todo...");
        $output = $this->exec($command);

        $output = explode("\n", $output);
        $last_line = array_pop($output);
        $url = trim($last_line);

        $this->output("Todo created: $url");

        $key = $this->input("Press O or B to open in browser, any other key to quit", null, null, true);

        $key = strtolower($key);
        if ($key == 'o' or $key == 'b')
        {
            $this->openInBrowser($url);
        }
    }

    protected $___add_gh = [
        "Add a new note - Github Issue",
    ];
	public function add_gh()
    {
        echo "Add a new note - Github Issue - Not yet implemented";
    }

    protected $___add_ml = [
        "Add a new note - E-mail Message (Email)",
    ];
	public function add_ml()
    {
        echo "Add a new note - E-mail Message (Email) - Not yet implemented";
    }

    protected $___add_qn = [
        "Add a new note - Miscellaneous - Quicknotes file",
    ];
	public function add_qn()
    {
        $this->clear();
        $message = $this->edit();
        $message = trim($message);
        if (empty($message))
        {
            $this->output("Cancelled");
            return;
        }

        // Old contents
        $prior_contents = file_get_contents($this->quicknotes_file);
        file_put_contents($this->quicknotes_file, $message."\n\n" . $prior_contents);
        $this->output("Complete");
    }

    protected $___add_tx = [
        "Add a new note - Text Message (TXT, SMS)",
    ];
	public function add_tx()
    {
        echo "Add a new note - Miscellaneous - Quicknotes file - Not yet implemented";
    }
}

// Kick it all off
Quicknote::run($argv);

// Note: leave this for packaging ?>
