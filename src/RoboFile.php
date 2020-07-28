<?php
/**
 * This is project's console commands configuration for Robo task runner.
 *
 * @see http://robo.li/
 */

class RoboFile extends \Robo\Tasks {

    const BUILD_DIR = "../build";

    const PROD_DIR = "/prod";
    const DEV_DIR = "/dev";

    // clean:prod
    public function cleanProd() {
        $this->_clean(self::PROD_DIR);
    }

    // clean:dev
    public function cleanDev() {
        $this->_clean(self::DEV_DIR);
    }

    private function _clean($dir) {
        $this->taskCleanDir([self::BUILD_DIR.$dir])->run();
    }
    
    // build:prod
    public function buildProd() {
        $this->_build(self::PROD_DIR, "production");
    }

    // build:dev
    public function buildDev() {
        $this->_build(self::DEV_DIR);
    }

    private function _build($dir, $config = "development") {

        if (!file_exists(self::BUILD_DIR)) {
            $this->taskFilesystemStack()
                ->mkdir(self::BUILD_DIR)
                ->run();
        }

        $fullDir = self::BUILD_DIR.$dir;
        
        if (file_exists($fullDir)) {
            $this->_clean($dir);
        }

        $this->taskFilesystemStack()
            ->mkdir($fullDir)
            ->mkdir($fullDir."/App")
            ->mkdir($fullDir."/public")
            ->mkdir($fullDir."/vendor")
            ->run();

        $this->taskCopyDir([
                "App" => $fullDir."/App",
                "vendor" => $fullDir."/vendor"
            ])->run();

        $this->taskFilesystemStack()->copy("./public/index.php", $fullDir."/public/index.php")->run();

        if ($config == "development" || $config == "production") {

            $configFile = "./Config/config-".$config.".php";
            if (file_exists($configFile)) {
                $this->taskFilesystemStack()->copy($configFile, $fullDir."/Config/config.php")->run();
            } else {
                $this->say("ERROR: Configuration file ".$configFile." not found. Incomplete build.");
            }

            $webConfigFile = "./public/Web-".$config.".config";
            if (file_exists($webConfigFile)) {
                $this->taskFilesystemStack()->copy($webConfigFile, $fullDir."/public/Web.config")->run();
            } else {
                $this->say("ERROR: IIS configuration file ".$configFile." not found. Incomplete build.");
            }

        } else {
            $this->taskFilesystemStack()->copy("./Config/config.php.template", $fullDir."/Config/config.php")->run();
            $this->taskFilesystemStack()->copy("./public/Web.config.template", $fullDir."/public/Web.config")->run();
        }

    }

    // update:dev
    public function updateDev() {
        $fullDir = self::BUILD_DIR.self::DEV_DIR;

        $this->taskCleanDir([$fullDir."/App", $fullDir."/public"])->run();

        $this->taskCopyDir([
            "App" => $fullDir."/App",
            "public" => $fullDir."/public"
        ])->run();
    }

}