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
        echo "Add a new note - TFX List -  Not yet implemented";

        $list = $this->select([
            "DN - DevNext - Bootcamp, CDT, PP, etc.",
            "EM - Weekly E-mail",
            "KS - Knowledge Sharing",
            "KZ - Kelly's List",
            "OP - Ops List",
            "TI - Team Initiative",
        ], "Select list to add to");
    }

    protected $___add_gh = [
        "Add a new note - Github Issue",
    ];
	public function add_gh()
    {
        echo "Add a new note - TFX List - Not yet implemented";
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
