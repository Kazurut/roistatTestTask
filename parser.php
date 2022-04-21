<?php
ini_set('max_execution_time', 0);

require_once 'functions.php';

class Parser
{
    const PATTERN = '/^([\d\.]+)[\s|-]+\[[\S ]+\][ "\w]+([\/a-z]+[\S\s]+?)"\s(\d+)[-\s]+(\d+)[\S\s]+?"[\s\S]+?\)[\s]?(Google|Bing|Baidu|Yandex)*.*$/m';

    public int $views = 0;
    public int $uniqUrls = 0;
    public int $traffic = 0;
    public array $urls = [];
    public array $crawlers = [
        'Google' => 0,
        'Bing' => 0,
        'Baidu' => 0,
        'Yandex' => 0
    ];
    public array $statusCodes = [];

	function __construct($fileName)
	{
        if (fileExists($fileName)) {
            $fileResource = $this->openFile($fileName);
            $this->readFile($fileResource);
            $this->summaryUniqueUrl();
            $this->prepareJson();
        } else {
            print_r('Error filename');
        }
	}

    public function openFile(string $fileName)
    {
        return openFile($fileName, 'r');
    }

    public function readFile($fileResource)
    {
        if ($fileResource) {
            while (($buffer = fgets($fileResource)) !== false) {
                $this->views++;
                preg_match_all(self::PATTERN, $buffer, $matches);

                if ($matches[2][0]) {
                    $this->getUniqUrls($matches[2][0]);
                }
                if ($matches[3][0]) {
                    $this->getStatusCode($matches[3][0]);
                }
                if (
                    $matches[4][0]
                    && $matches[3][0] >= 200
                    && $matches[3][0] <= 299
                ) {
                    $this->summaryTraffic($matches[4][0]);
                }
                if ($matches[5][0]) {
                    $this->getCrawler($matches[5][0]);
                }
            }
        }
    }

    public function getCrawler(string $crawler)
    {
        if (arrayKeyExists($crawler, $this->crawlers)) {
            $this->crawlers[$crawler]++;
        } else {
            $this->crawlers[$crawler] = 1;
        }
    }

    public function getStatusCode(string $statusCode)
    {
        if (arrayKeyExists($statusCode, $this->statusCodes)) {
            $this->statusCodes[$statusCode]++;
        } else {
            $this->statusCodes[$statusCode] = 1;
        }
    }

    public function summaryTraffic(int $trafficValue)
    {
        $this->traffic += $trafficValue;
    }

    public function getUniqUrls($url)
    {
        if (arrayKeyExists($url, $this->urls)) {
            $this->urls[$url]++;
        } else {
            $this->urls[$url] = 1;
        }
    }

    public function summaryUniqueUrl()
    {
        $this->uniqUrls = count($this->urls);
    }

    public function prepareJson()
    {
        unset($this->urls);
        print_r(json_encode($this, JSON_PRETTY_PRINT));
    }
}

$parser = new Parser($argv[1]);