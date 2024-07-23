<?php
if ($bot_run_as) {

    class Logger {
        /*
        * Developed by rainfd and lxyddice
        */
        private array $urls;
        private array $levels = ["TRACE", "DEBUG", "INFO", "WARNING", "ERROR", "CRITICAL"];
        private int $logLowLimit;

        public function __construct(array $urls) {
            global $bot_run_as;
            $this->urls = $urls;
            $this->logLowLimit = $bot_run_as["config"]["logger"]["sendLevel"];
        }

        private function useLogToolTogether(array $data): bool {
            global $bot_run_as;
            if ($this->$bot_run_as["config"]["logger"]["useLogToolTogether"]) {
                $levelMap = [
                    "TRACE" => 0,
                    "DEBUG" => 0,
                    "INFO" => 1,
                    "WARNING" => 2,
                    "ERROR" => 3,
                    "CRITICAL" => 4,
                ];
                // Assuming tool_log and DingraiaPHPAddNormalResponse are defined elsewhere
                tool_log($levelMap[$data["level"]], base64_decode($data["message"]));
                $r = DingraiaPHPAddNormalResponse("tool_log", []);
                return $r;
            }
            return false;
        }

        private function log(string $level, $message): bool {
    //        exit($level);
            global $bot_run_as;
            $message = $this->message_checker($message);
            if (!$bot_run_as["config"]["logger"]["open"]) {
                return false;
            }

            $levelPosition = array_search($level, $this->levels);
            if ($levelPosition < $this->logLowLimit) {
                return false;
            }

            $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
            $caller = $backtrace[1] ?? [];
            $data = [
                'level' => $level,
                'message' => base64_encode($message),
                'file' => str_replace($bot_run_as["indexDir"]."/", "", $caller["file"]) ?? "Unknown file",
                'line_no' => $caller['line'] ?? null,
                'function' => $caller['function'] ?? null,
                'timestamp' => microtime(true),
            ];

            $this->sendLog($data);
            return true;
        }

        private function sendLog(array $data) {
            
            global $bot_run_as;
            $logFile = "data/bot/logger/f/{$bot_run_as['RUN_ID']}.json";
            if (!is_dir("data/bot/logger/f")) {
                mkdir("data/bot/logger/f", 0777, true);
                mkdir("data/bot/logger/c", 0777, true);
            }
            if (!file_exists($logFile)) {
                write_to_file_json($logFile, $data);
            }
            if ($data["message"] == "RU5E" && $data["level"] == "TRACE") {
                copy($logFile, "data/bot/logger/c/{$bot_run_as['RUN_ID']}.json");
                unlink($logFile);
                return $data;
            } else {
                $f = read_file_to_array($logFile);
                $f[] = $data;
                write_to_file_json($logFile, $f);
                return $data;
            }
            
            if (empty($this->urls)) {
                return false;
            }

            foreach ($this->urls as $url) {
                $response = requests("POST", $url, $data, ["Content-Type" => "application/json"], 1);
                if ($response && $response["body"]) {
                    $response["url"] = $url;
                    DingraiaPHPAddNormalResponse("loggerCode", $response, true);
                }
            }
            return $r;
        }

        private function message_checker($message): string
        {
            if (!is_string($message)) {
                $message = json_encode($message, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
            }
            return $message;
        }

        // Public methods for logging at different levels
        public function trace($message): bool {
            return $this->log('TRACE', $message);
        }

        public function debug($message): bool {
            return $this->log('DEBUG', $message);
        }

        public function info($message): bool {
            return $this->log('INFO', $message);
        }

        public function success($message): bool {
            return $this->log('SUCCESS', $message);
        }

        public function warning($message): bool {
            return $this->log('WARNING', $message);
        }

        public function error($message): bool {
            return $this->log('ERROR', $message);
        }

        public function critical($message): bool {
            return $this->log('CRITICAL', $message);
        }
    }
    $logger["conf"] = $bot_run_as["config"]["logger"];
    $bot_run_as["logger"]["class"] = new Logger($logger["conf"]["urls"]);
}