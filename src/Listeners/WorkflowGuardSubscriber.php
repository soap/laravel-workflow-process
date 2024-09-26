<?php

namespace Soap\LaravelWorkflowProcess\Listeners;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use ZeroDaHero\LaravelWorkflow\Events\GuardEvent;

class WorkflowGuardSubscriber
{
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
            $guard = $metaData['guard'];
            $expressionLanguage = new ExpressionLanguage;
            $result = $expressionLanguage->evaluate($guard, [
                'authenticated' => auth()->check(),
                'subject' => $subject,
                'user' => auth()->user(),
            ]);
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
