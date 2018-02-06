<?php
declare(strict_types=1);

namespace BehatCoverageExtension;

use Webmozart\Assert\Assert;
use LiveCodeCoverage\CodeCoverageFactory;
use SebastianBergmann\CodeCoverage\CodeCoverage;

final class RemoteCodeCoverage
{
    /**
     * @private
     */
    private $coverageId;

    /**
     * @var CodeCoverage
     */
    private $codeCoverage;

    /**
     * @var string
     */
    private $storageDirectory;

    private function __construct(CodeCoverage $codeCoverage, $storageDirectory, $coverage_id)
    {
        $this->codeCoverage = $codeCoverage;
        $this->coverageId = $coverage_id;

        // TODO move to storage utility
        if (!is_dir($storageDirectory)) {
            if (!mkdir($storageDirectory, 0777, true) && !is_dir($storageDirectory)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $storageDirectory));
            }
        }
        $this->storageDirectory = $storageDirectory;
    }

    /**
     * @param $storageDirectory
     * @param null $phpunitConfigFilePath
     * @param string $coverage_id
     * @return self
     */
    public static function bootstrap($storageDirectory, $phpunitConfigFilePath = null, $coverage_id = 'live-coverage')
    {
        if ($phpunitConfigFilePath !== null) {
            Assert::file($phpunitConfigFilePath);
            $codeCoverage = CodeCoverageFactory::createFromPhpUnitConfiguration($phpunitConfigFilePath);
        } else {
            $codeCoverage = CodeCoverageFactory::createDefault();
        }

        $liveCodeCoverage = new self($codeCoverage, $storageDirectory, $coverage_id);

        $liveCodeCoverage->start();

        // TODO add a register shutdown option (may be needed for legacy code that exists at certain places)

        return $liveCodeCoverage;
    }

    private function start()
    {
        $this->codeCoverage->start($this->coverageId);
    }

    public function stopAndSave()
    {
        $this->codeCoverage->stop();

        // TODO use storage utility
        $cov = '<?php return unserialize(' . var_export(serialize($this->codeCoverage), true) . ');';
        file_put_contents($this->storageDirectory . '/' . $this->generateCovFileName(), $cov);
    }

    private function generateCovFileName()
    {
        return uniqid(date('YmdHis'), true) . '.cov';
    }
}
