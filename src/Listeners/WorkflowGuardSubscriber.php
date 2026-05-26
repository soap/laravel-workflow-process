<?php

namespace Soap\LaravelWorkflowProcess\Listeners;

use Soap\LaravelWorkflowProcess\GuardEvaluator;
use Soap\LaravelWorkflowProcess\WorkflowProcess;
use ZeroDaHero\LaravelWorkflow\Events\GuardEvent;

class WorkflowGuardSubscriber
{
    protected GuardEvaluator $guardEvaluator;

    protected WorkflowProcess $workflowProcess;

    public function __construct(GuardEvaluator $guardEvaluator, WorkflowProcess $workflowProcess)
    {
        $this->guardEvaluator = $guardEvaluator;
        $this->workflowProcess = $workflowProcess;
    }

    public function handleOnGuard(GuardEvent $event): void
    {
        $transition = $event->getTransition();
        $metaData = $event->getWorkflow()->getMetadataStore()->getTransitionMetadata($transition);

        if (isset($metaData['guard'])) {
            $variables = [
                'subject' => $event->getSubject(),
                'authenticated' => $this->workflowProcess->getAuthenticated(),
                'user' => $this->workflowProcess->getUser(),
            ];

            $result = $this->guardEvaluator->evaluate($metaData['guard'], $variables);

            if (! $result) {
                $event->setBlocked(true, 'Guard blocked');
            }
        }
    }

    public function subscribe($events): void
    {
        $events->listen(
            'workflow.guard',
            [WorkflowGuardSubscriber::class, 'handleOnGuard']
        );
    }
}
