<?php

namespace Soap\LaravelWorkflowProcess\Listeners;

use Soap\LaravelWorkflowProcess\GuardEvaluator;
use ZeroDaHero\LaravelWorkflow\Events\GuardEvent;

class WorkflowGuardSubscriber
{
    /**
     * The guard evaluator instance.
     *
     * @var GuardEvaluator
     */
    protected $guardEvaluator;

    /**
     * Create a new subscriber instance.
     */
    public function __construct(GuardEvaluator $guardEvaluator)
    {
        $this->guardEvaluator = $guardEvaluator;
    }

    /**
     * Handle the event.
     */
    public function handleOnGuard(GuardEvent $event): void
    {
        // This is a call by using event proxy to Symfony GuardEvent
        $subject = $event->getSubject();
        $transition = $event->getTransition();

        $workflow = $event->getWorkflow();
        $metaData = $workflow->getMetadataStore()->getTransitionMetadata($transition);

        if (isset($metaData['guard'])) {
            $guardExpression = $metaData['guard'];
            // Prepare variables to pass to the evaluator.
            // You can pass the workflow subject, user, or any other required objects.
            $variables = [
                'subject' => $event->getSubject(),
            ];
            $workflowProcess = app('workflow-process');

            $authenticated = $workflowProcess->getAuthenticated();
            $user = $workflowProcess->getUser();

            $variables['authenticated'] = $authenticated;

            // Optionally include the authenticated user.
            if ($authenticated) {
                $variables['user'] = $workflowProcess->getUser();
            }

            // Evaluate the guard expression using the GuardEvaluator.
            $result = $this->guardEvaluator->evaluate($guardExpression, $variables);

            // If the expression evaluates to false, block the transition.
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
