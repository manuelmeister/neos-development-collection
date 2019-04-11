<?php
declare(strict_types=1);
namespace Neos\EventSourcedNeosAdjustments\NodeImportFromLegacyCR\Command;

/*
 * This file is part of the Neos.ContentRepositoryMigration package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\EventSourcedNeosAdjustments\NodeImportFromLegacyCR\Service\ContentRepositoryExportService;
use Neos\EventSourcedNeosAdjustments\NodeImportFromLegacyCR\Service\ImportProjectionPerformanceService;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Cli\CommandController;

/**
 * @Flow\Scope("singleton")
 */
class ContentRepositoryMigrateCommandController extends CommandController
{

    /**
     * @Flow\Inject
     * @var ContentRepositoryExportService
     */
    protected $contentRepositoryExportService;

    /**
     * @Flow\Inject
     * @var ImportProjectionPerformanceService
     */
    protected $importProjectionPerformanceService;

    /**
     * Run a CR export
     */
    public function runCommand()
    {
        $this->contentRepositoryExportService->reset();
        $this->importProjectionPerformanceService->configureGraphAndWorkspaceProjectionsToRunSynchronously();
        $this->contentRepositoryExportService->migrate();

        // TODO: re-enable asynchronous behavior; and trigger catchup of all projections. (e.g. ChangeProjector etc)
        $this->outputLine('');
        $this->outputLine('');
        $this->outputLine('!!!!! NOW, run ./flow projection:catchup change');
        $this->outputLine('!!!!! NOW, run ./flow projection:catchup nodehiddenstate');

        // ChangeProjector catchup



    }
}
