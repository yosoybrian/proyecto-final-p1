# To learn more about how to use Nix to configure your environment
# see: https://firebase.google.com/docs/studio/customize-workspace
{ pkgs, ... }: {
  # Which nixpkgs channel to use.
  channel = "stable-24.05"; # or "unstable"

  # Use https://search.nixos.org/packages to find packages
  packages = [
    pkgs.php82
    pkgs.php82Packages.composer 
    pkgs.nodejs_18
    pkgs.git
    pkgs.github-cli   
    pkgs.bc    
  ];

  # Sets environment variables in the workspace
  env = {};
  idx = {
    # Search for the extensions you want on https://open-vsx.org/ and use "publisher.id"
    extensions = [
      # "vscodevim.vim"
        "bmewburn.vscode-intelephense-client"
        "ritwickdey.liveserver"
        "xdebug.php-debug"
        "devsense.phptools-vscode"
        "wongjn.php-sniffer"
        "recca0120.vscode-phpunit"        
        "devsense.composer-php-vscode"
         # Testing Extensions
        "ms-vscode.test-adapter-converter"
        "hbenl.vscode-test-explorer"
        # Development Tools
        "esbenp.prettier-vscode"
        "ms-vscode.vscode-json"
        "redhat.vscode-yaml"
        "ms-vscode.live-server"
        "ritwickdey.liveserver"
        # Git and GitHub
        "github.vscode-pull-request-github"
        "github.copilot"
        # Utility Extensions
        "ms-vscode.vscode-todo-highlight"
        "streetsidesoftware.code-spell-checker"
    ];

    # Enable previews
    previews = {
      enable = true;
      previews = {
        # web = {
        #   # Example: run "npm run dev" with PORT set to IDX's defined port for previews,
        #   # and show it in IDX's web preview panel
        #   command = ["npm" "run" "dev"];
        #   manager = "web";
        #   env = {
        #     # Environment variables to set for your server
        #     PORT = "$PORT";
        #   };
        # };
      };
    };

    # Workspace lifecycle hooks
    workspace = {
      # Runs when a workspace is first created
      onCreate = {
        # Example: install JS dependencies from NPM
        # npm-install = "npm install";
        run-setup = ".devcontainer/setup.sh";
        default.openFiles = ["README.md"];
      };
      # Runs when the workspace is (re)started
      onStart = {
        # Example: start a background task to watch and re-build backend code
        # watch-backend = "npm run watch-backend";
      };
    };
  };
}
