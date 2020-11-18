<?php

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Symfony\Component\Filesystem\Filesystem as FileSystem;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context
{
    private $filesystem;
    private $output;
    private $env = '';

    public function __construct()
    {
        $this->filesystem = new FileSystem();
        $this->path = sys_get_temp_dir() . '/' . base64_encode(random_bytes(32)) . '/';
    }

    /**
     * @Given the :var environment variable is set to :value
     */
    public function setEnvVar($var, $value)
    {
        $this->env = "$var=$value";
    }

    /**
     * @Given the :var environment variable is not set
     * @afterScenario
     */
    public function resetEnv()
    {
        $this->env = '';
    }

    /**
     * @beforeScenario
     */
    public function createFiles()
    {
        $this->filesystem->mkdir($this->path);

        $this->filesystem->mirror(__DIR__ . '/../../vendor', $this->path . 'vendor');
        $this->filesystem->mirror(__DIR__ . '/../../src', $this->path . 'src');
        $this->filesystem->copy(__DIR__ . '/../../phpspec.yaml', $this->path . 'phpspec.yaml');

        chdir($this->path);
    }

    /**
     * @Given the file :path contains:
     */
    public function createFileContaining($path, PyStringNode $contents)
    {
        $this->filesystem->dumpFile($this->path . $path ,  $contents);
    }

    /**
     * @afterScenario
     */
    public function cleanFiles()
    {
        $this->filesystem->remove($this->path);
    }

    /**
     * @When I run phpspec
     */
    public function iRunPhpspec()
    {
        $phpSpecCmd = $this->env . ' vendor/bin/phpspec r --no-interaction';
        if(!$this->output = shell_exec($phpSpecCmd)) {
            throw new Exception('No output from phpspec');
        }
    }

    /**
     * @Then I should see:
     */
    public function iShouldSee(PyStringNode $string)
    {
        if(!str_contains($this->output, $string)) {
            throw new Exception("Did not find expected string in actual:\n" . $this->output);
        }
    }

    /**
     * @Then I should not see:
     */
    public function iShouldNotSee(PyStringNode $string)
    {
        if(str_contains($this->output, $string)) {
            throw new Exception("Found unexpected string in output");
        }
    }
}
