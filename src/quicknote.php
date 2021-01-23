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

    protected $__pacli_exec = "PACLI Exec";
    public $pacli_exec = "/usr/local/bin/pacli";

    protected $__pgh_exec = "PGH Exec";
    public $pgh_exec = "/usr/local/bin/pgh";

    protected $__ptfx_exec = "PTFX Exec";
    public $ptfx_exec = "/usr/local/bin/ptfx";

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
        // Initilize PACLI for use here
        $__no_direct_run__ = true;
        ob_start();
        require_once($this->pacli_exec);
        $output = ob_get_clean();
        $pacli = new Pacli();
        $pacli->initConfig();

        // Get workspace
        $response = $pacli->get('workspaces', false);
        $workspace_gid = $response->data[0]->gid;

        // Get favorite projects
        //$response = $pacli->get('users/me/favorites?resource_type=project&workspace=' . $workspace_gid, false);

        // Get all non-archived projects
        $response = $pacli->get('projects?archived=0', false);
        $data = $response->data;
        $projects = [
            'NO PROJECT (default)' => false,
        ];
        foreach ($data as $project)
        {
            $projects[$project->name] = $project->gid;
        }
        $project_keys = array_keys($projects);

        // Select project to add to (or no project);
        $project_name = $this->select($project_keys, "Select project");
        $project_gid = $projects[$project_name];
        //$this->output($project_name . ": " . $project_gid);

        if (!empty($project_gid))
        {
            // Get sections within project if any
            $response = $pacli->get('projects/'.$project_gid.'/sections', false);
            $section_gid = false;
            if (!empty($response->data) and count($response->data) > 1)
            {
                $data = $response->data;
                $sections = [
                    'NO SECTION (default)' => false,
                ];
                foreach ($data as $section)
                {
                    $sections[$section->name] = $section->gid;
                }
                $section_keys = array_keys($sections);

                // Select section to add to (or no section);
                $this->clear();
                $section_name = $this->select($section_keys, "Select section");
                $section_gid = $sections[$section_name];
                //$this->output($section_name . ": " . $section_gid);
            }
        }

        $assignee = empty($project_gid) ? "me" : "";
        $due_date = "";
        $task_name = "";
        $task_notes = "";
        $try_again = false;

        do {
            $warnings = [];

            $edited = $this->edit(

                "$task_name\n" . 
                self::EDIT_LINE_BREAK . "\n" .

                "Due Date: '$due_date'\n" .
                self::EDIT_LINE_BREAK . "\n" .

                "Assignee: '$assignee'\n" .
                self::EDIT_LINE_BREAK . "\n" .

                "Notes Below:\n" .
                self::EDIT_LINE_BREAK . "\n" .
                $task_notes . "\n" .
                self::EDIT_LINE_BREAK . "\n" .

                "HELP/TIPS:\n" . 
                "Due Date: Any parseable date string\n" . 
                "Assignee: 'me' or blank\n" . 
                "",
                
                "new_asana_task.md",

                !empty($task_name)
            );

            $data = explode(self::EDIT_LINE_BREAK, $edited);
            $data = array_map('trim', $data);

            $task_name = $data[0];
            $due_date = trim(preg_replace("/Due Date: '(.*)'$/", "$1", $data[1]));
            $assignee = trim(preg_replace("/Assignee: '(.*)'$/", "$1", $data[2]));
            $task_notes = $data[4];

            $data = [
                'workspace' => $workspace_gid,
                'name' => $task_name,
                'notes' => $task_notes,
            ];

            // No name will show up as a blank task
            if (empty($task_name))
            {
                $warnings[]= "No task name entered";
            }

            if (!empty($due_date))
            {
                $due_date_stamp = strtotime($due_date);
                if ($due_date_stamp)
                {
                    $data['due_on'] = date('Y-m-d', $due_date_stamp);
                }
                else
                {
                    $warnings[]= "Invalid Due Date - $due_date";
                }
            }

            if (!empty($assignee))
            {
                if ($assignee == 'me')
                {
                    //$result = $pacli->get("users/me", false, false, true);
                    //$data['assignee'] = $result->data->gid;
                    $data['assignee'] = $assignee;
                }
                else
                {
                    $warnings[]= "Invalid Assignee - $assignee";
                }
            }

            if (empty($project_gid))
            {
                if (empty($data['due_on']))
                {
                    $warnings[]= "No due date and no project - task may get lost";
                }
                if (empty($data['assignee']))
                {
                    $warnings[]= "No assignee and no project - task will get lost!";
                }
            }
            else
            {
                $data['projects'] = [$project_gid];
            }

            $try_again = false;
            if (!empty($warnings))
            {
                $this->warn("\n - " . join("\n - ", $warnings));
                $option = $this->select([
                    "Go back and modify data (default)",
                    "Create task anyway",
                ]);

                if ($option == "Go back and modify data (default)")
                {
                    $try_again = true;
                }
            }

        } while ($try_again);

        $result = $pacli->post("tasks", $data, false, false, true);

        if (empty($result->data->permalink_url))
        {
            $this->error("There was an error creating the task", false);
            $this->error($result);
        }

        // Move task to section if specified
        if (!empty($section_gid))
        {
            $pacli->post("sections/$section_gid/addTask", ['task' => $result->data->gid], false, false, true);
        }

        // provide & offer to open full screen link
        $this->_success_maybe_open($result->data->permalink_url . "/f");
    }

    protected $___add_cl = [
        "Add a new note - Calendar Event",
    ];
	public function add_cl()
    {
        echo "Add a new note - Calendar Event - Not yet implemented";
    }

    protected $___add_fx = [
        "Add a new note - TFX List",
    ];
	public function add_fx()
    {
        $this->clear();

        $option_configs = QN_TFX_List_Options::$options;
        $options = [];
        foreach ($option_configs as $key => $option_config)
        {
            $name = $option_config['name'];
            $options[$name] = $key;
        }

        $option_keys = array_keys($options);

        $option = $this->select($option_keys, "Select area to add item", 4);
        $option_key = $options[$option];
        $option_config = $option_configs[$option_key];

        $id = $option_config["id"];
        $type = $option_config["type"];
        $template = empty($option_config["template"]) ? "" : $option_config["template"];
        $assign = empty($option_config["assign"]) ? "" : $option_config["assign"];
        $location = $option_config["location"];

        // Initilize PTFX for use here
        $__no_direct_run__ = true;
        ob_start();
        require_once($this->ptfx_exec);
        $output = ob_get_clean();
        $ptfx = new Ptfx();
        $ptfx->initConfig();

        $this->clear();
        $this->output("Creating todo...");
        $url = $ptfx->create_todo(
            $id,        // list or project id
            null,       // message (will prompt)
            $assign,    // user to assign
            null,       // due date (will prompt)
            $location,  // where to creat - top or bottom of list
            $type,      // type of id - list or project
            $template,  // message template
        );

        $this->_success_maybe_open($url);
    }

    protected $___add_gh = [
        "Add a new note - Github Issue",
    ];
	public function add_gh()
    {
        // Initilize PGH for use here
        $__no_direct_run__ = true;
        ob_start();
        require_once($this->pgh_exec);
        $output = ob_get_clean();
        $pgh = new Pgh();
        $pgh->initConfig();

        // Get all user's repositories
        $results = $pgh->get("users/" . $pgh->api_username . "/repos", false);

        // Select repository
        $repos = [];
        foreach ($results as $repo)
        {
            $repo_listing = $repo->name . ": " . $repo->description . "( " . $repo->html_url . " )";
            $repos[$repo_listing] = $repo->name;
        }
        $repo_keys = array_keys($repos);

        // Select repo to add to (or no repo);
        $repo_listing = $this->select($repo_keys, "Select repository");
        $repo_name = $repos[$repo_listing];

        // Get list of existing issues
        $results = $pgh->get("repos/" . $pgh->api_username . "/" . $repo_name . "/issues", false);
        $issues = [];
        foreach ($results as $issue)
        {
            $issues[]= str_pad($issue->number, 3, " ", STR_PAD_LEFT) . ". " .
                str_pad("[" . $issue->state ."] ", 15, ".") . " " .
                $issue->title
            ;
        }
        $issues = implode("\n", $issues);
        $this->output($issues);

        // Edit loop to enter details of issue
        // - Name
        // - Description (md)
        // - Tags, assign, etc
        // - show existing issues for reference

        // Add issue to repository
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
        echo "Add a new note - Text Message - Not yet implemented";
    }

    /**
     * Output success message and offer to open URL of created item
     */
    protected function _success_maybe_open($url, $list_url=false)
    {
        do {
            $this->clear();
            $this->output("Item created: $url");
            $this->output(" - Press O to open item in browser");
            if ($list_url)
            {
                $this->output("On List: $list_url");
                $this->output(" - Press L to open item in browser");
            }

            $key = $this->input("\nAny other key to quit", null, null, true);

            $continue = false;
            $key = strtolower($key);
            if ($key == 'o')
            {
                $this->openInBrowser($url);
            }
            elseif ($key == 'l')
            {
                $this->openInBrowser($list_url);
            }
        } while ($continue);
    }
}

// Kick it all off
Quicknote::run($argv);

// Note: leave this for packaging ?>
