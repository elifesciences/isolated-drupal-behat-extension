<?php

namespace eLife\IsolatedDrupalBehatExtension;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use PHPUnit_Framework_Assert as Assert;
use RuntimeException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

final class FeatureContext implements Context
{
    /**
     * @var string
     */
    private $phpBin;

    /**
     * @var string
     */
    private $drushBin;

    /**
     * @var string
     */
    private $workingDir;

    /**
     * @var string
     */
    private $drupalDir;

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var string
     */
    private $dbUrl;

    /**
     * @var Process
     */
    private $behatProcess;

    /**
     * @var Process
     */
    private $serverProcess;

    public function __construct()
    {
        $phpFinder = new PhpExecutableFinder();
        if (false === $this->phpBin = $phpFinder->find()) {
            throw new RuntimeException('Unable to find the PHP executable.');
        }
        $this->workingDir = $this->getTemporaryDirectory(self::getBaseTestPath());
        $this->drupalDir = $this->workingDir . '/drupal';
        $this->drushBin = __DIR__ . '/../../vendor/bin/drush';
        if (false === file_exists($this->drushBin)) {
            throw new RuntimeException('Unable to find Drush');
        }
        $this->baseUrl = 'http://127.0.0.1:8080/';
        $this->dbUrl = 'sqlite:/db.sqlite';
        $this->behatProcess = new Process(null);
    }

    /**
     * Cleans test folders in the temporary directory.
     *
     * @BeforeSuite
     * @AfterSuite
     */
    public static function cleanTestFolders()
    {
        if (is_dir(self::getBaseTestPath())) {
            self::getFilesystem()
                ->chmod(self::getBaseTestPath(), 0777, 0000, true);
        }
        self::getFilesystem()->remove(self::getBaseTestPath());
    }

    /**
     * Downloads Drupal 7.
     *
     * @BeforeScenario
     */
    public function downloadDrupal7()
    {
        ProcessBuilder::create()
            ->setPrefix($this->drushBin)
            ->add('pm-download')
            ->add('drupal-7.38')
            ->add('--destination=' . $this->workingDir)
            ->add('--drupal-project-rename')
            ->add('--yes')
            ->setTimeout(600)
            ->getProcess()
            ->mustRun();
    }

    /**
     * Run server.
     *
     * @BeforeScenario
     */
    public function runServer()
    {
        $router = $this->drupalDir . '/.ht.router.php';
        if (!file_exists($router)) {
            $this->createFile(
                $router,
                file_get_contents(__DIR__ . '/.ht.router.php')
            );
        }

        $uri = parse_url($this->baseUrl);

        $this->serverProcess = ProcessBuilder::create()
            ->setPrefix('exec')
            ->add($this->phpBin)
            ->add('-S')
            ->add(sprintf('%s:%s', $uri['host'], $uri['port']))
            ->add($router)
            ->setWorkingDirectory($this->drupalDir)
            ->getProcess();

        $this->serverProcess->start();

        sleep(1);

        if (!$this->serverProcess->isRunning()) {
            throw new RuntimeException('PHP built-in server process terminated immediately');
        }

        // Also trap fatal errors to make sure running processes are terminated.
        register_shutdown_function([$this, 'stopServer']);
    }

    /**
     * Stop server.
     *
     * @AfterScenario
     */
    public function stopServer()
    {
        if ($this->serverProcess && $this->serverProcess->isRunning()) {
            $this->serverProcess->stop();
        }
    }

    /**
     * Runs Behat with provided parameters.
     *
     * @When /^I run "behat(?: ((?:\"|[^"])*))?"$/
     *
     * @param string $argumentsString
     */
    public function iRunBehat($argumentsString = '')
    {
        $argumentsString = strtr($argumentsString, ['\'' => '"']);
        $this->behatProcess->setWorkingDirectory($this->workingDir);
        $this->behatProcess->setCommandLine(
            sprintf(
                '%s %s %s %s',
                $this->phpBin,
                escapeshellarg(BEHAT_BIN_PATH),
                $argumentsString,
                strtr(
                    '--format-settings=\'{"timer": false}\' --no-colors',
                    ['\'' => '"', '"' => '\"']
                )
            )
        );
        $this->behatProcess->start();
        $this->behatProcess->wait();
    }

    /**
     * Checks whether previously run command passes|fails with provided output.
     *
     * @Then /^it should (fail|pass) with:$/
     *
     * @param string $success 'fail' or 'pass'
     * @param PyStringNode $text
     */
    public function itShouldPassWith($success, PyStringNode $text)
    {
        $this->itShouldFail($success);
        $this->theOutputShouldContain($text);
    }

    /**
     * Checks whether last command output contains provided string.
     *
     * @Then the output should contain:
     *
     * @param PyStringNode $text
     */
    public function theOutputShouldContain(PyStringNode $text)
    {
        Assert::assertContains(
            $this->getExpectedOutput($text),
            $this->getOutput()
        );
    }

    /**
     * @param PyStringNode $expectedText
     *
     * @return string
     */
    private function getExpectedOutput(PyStringNode $expectedText)
    {
        $text = strtr($expectedText, ['\'\'\'' => '"""']);
        // windows path fix
        if ('/' !== DIRECTORY_SEPARATOR) {
            $text = preg_replace_callback(
                '/ features\/[^\n ]+/',
                function ($matches) {
                    return str_replace('/', DIRECTORY_SEPARATOR, $matches[0]);
                },
                $text
            );
            $text = preg_replace_callback(
                '/\<span class\="path"\>features\/[^\<]+/',
                function ($matches) {
                    return str_replace('/', DIRECTORY_SEPARATOR, $matches[0]);
                },
                $text
            );
            $text = preg_replace_callback(
                '/\+[fd] [^ ]+/',
                function ($matches) {
                    return str_replace('/', DIRECTORY_SEPARATOR, $matches[0]);
                },
                $text
            );
        }

        return $text;
    }

    /**
     * Checks whether previously run command failed|passed.
     *
     * @Then /^it should (fail|pass)$/
     *
     * @param string $success 'fail' or 'pass'
     */
    public function itShouldFail($success)
    {
        if ('fail' === $success) {
            if (0 === $this->getExitCode()) {
                echo 'Actual output:' . PHP_EOL . PHP_EOL . $this->getOutput();
            }
            Assert::assertNotEquals(0, $this->getExitCode());
        } else {
            if (0 !== $this->getExitCode()) {
                echo 'Actual output:' . PHP_EOL . PHP_EOL . $this->getOutput();
            }
            Assert::assertEquals(0, $this->getExitCode());
        }
    }

    /**
     * @return integer
     */
    private function getExitCode()
    {
        return $this->behatProcess->getExitCode();
    }

    /**
     * @return string
     */
    private function getOutput()
    {
        $output = $this->behatProcess->getErrorOutput() . $this->behatProcess->getOutput();
        // Normalize the line endings in the output.
        if ("\n" !== PHP_EOL) {
            $output = str_replace(PHP_EOL, "\n", $output);
        }

        return trim(preg_replace("/ +$/m", '', $output));
    }

    /**
     * @Given /^Drupal is installed$/
     */
    public function drupalIsInstalled()
    {
        ProcessBuilder::create()
            ->setPrefix($this->drushBin)
            ->add('site-install')
            ->add('minimal')
            ->add('--account-name=admin')
            ->add('--account-pass=password')
            ->add('--db-url=sqlite:' . sys_get_temp_dir() . '/master.sqlite')
            ->add('--yes')
            ->setWorkingDirectory($this->drupalDir)
            ->setTimeout(600)
            ->setEnv('PHP_OPTIONS', '-d sendmail_path=' . `which true`)
            ->getProcess()
            ->mustRun();
    }

    /**
     * @Given the :arg1 variable is set to :arg2
     *
     * @param string $variable
     * @param string $value
     */
    public function theVariableIsSetTo($variable, $value)
    {
        ProcessBuilder::create()
            ->setPrefix($this->drushBin)
            ->add('variable-set')
            ->add($variable)
            ->add($value)
            ->add('--exact')
            ->add('--yes')
            ->setWorkingDirectory($this->drupalDir)
            ->getProcess()
            ->mustRun();
    }

    /**
     * Creates a file with specified name and context in current working
     * directory.
     *
     * @Given /^(?:there is )?a file named "([^"]*)" with:$/
     *
     * @param   string $filename name of the file (relative path)
     * @param   PyStringNode $content
     */
    public function aFileNamedWith($filename, PyStringNode $content)
    {
        $content = strtr((string) $content, ["'''" => '"""']);
        $this->createFile($this->workingDir . '/' . $filename, $content);
    }

    /**
     * @param string $filename
     * @param string $content
     */
    private function createFile($filename, $content)
    {
        $content = strtr(
            $content,
            [
                '{{ base_url }}' => $this->baseUrl,
                '{{ drush }}' => $this->drushBin,
                '{{ db_url }}' => $this->dbUrl,
                '{{ drupal_root }}' => $this->drupalDir,
            ]
        );

        $this->getFilesystem()->dumpFile($filename, $content);
    }

    /**
     * @param string $basePath
     *
     * @return string
     */
    private function getTemporaryDirectory($basePath)
    {
        return $basePath . '/' . md5(uniqid('', true));
    }

    /**
     * @return string
     */
    private static function getBaseTestPath()
    {
        return __DIR__ . '/../../tmp';
    }

    /**
     * @return Filesystem
     */
    private static function getFilesystem()
    {
        static $filesystem;

        if (empty($filesystem)) {
            $filesystem = new Filesystem();
        }

        return $filesystem;
    }
}
