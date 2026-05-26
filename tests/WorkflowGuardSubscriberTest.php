<?php

use Illuminate\Contracts\Auth\Authenticatable;
use Soap\LaravelWorkflowProcess\GuardEvaluator;
use Soap\LaravelWorkflowProcess\Listeners\WorkflowGuardSubscriber;
use Soap\LaravelWorkflowProcess\WorkflowProcess;
use Symfony\Component\Workflow\Marking;
use Symfony\Component\Workflow\Metadata\MetadataStoreInterface;
use Symfony\Component\Workflow\Transition;
use Symfony\Component\Workflow\Workflow;
use ZeroDaHero\LaravelWorkflow\Events\GuardEvent;

beforeEach(function () {
    $this->subject = new stdClass;
    $this->transition = $this->createMock(Transition::class);
    $this->marking = new Marking;
    $this->metadataStore = $this->createMock(MetadataStoreInterface::class);

    $this->workflow = $this->createMock(Workflow::class);
    $this->workflow->expects($this->any())
        ->method('getMetadataStore')
        ->willReturn($this->metadataStore);

    $this->workflowProcessMock = $this->createMock(WorkflowProcess::class);
    $this->workflowProcessMock->method('getAuthenticated')->willReturn(false);
    $this->workflowProcessMock->method('getUser')->willReturn(null);
});

it('blocks transition when evaluation returns false', function () {
    // Set expectation on metadata store.
    $this->metadataStore->expects($this->once())
        ->method('getTransitionMetadata')
        ->with($this->transition)
        ->willReturn([
            'guard' => 'some_expression',
        ]);

    // Create a mock GuardEvaluator that returns false.
    $guardEvaluatorMock = $this->createMock(GuardEvaluator::class);
    $guardEvaluatorMock->expects($this->once())
        ->method('evaluate')
        ->with(
            'some_expression',
            $this->callback(function ($variables) {
                // Ensure that the subject is passed in the evaluation variables.
                return isset($variables['subject']) && $variables['subject'] === $this->subject;
            })
        )
        ->willReturn(false);

    // Instantiate the subscriber with the mocked GuardEvaluator.
    $subscriber = new WorkflowGuardSubscriber($guardEvaluatorMock, $this->workflowProcessMock);

    // Create the GuardEvent using the shared objects.
    $event = new GuardEvent($this->subject, $this->marking, $this->transition, $this->workflow);

    // Trigger the guard event.
    $subscriber->handleOnGuard($event);

    // Assert that the transition is blocked.
    expect($event->isBlocked())->toBeTrue();
});

it('allows transition when evaluation returns true', function () {
    // Set expectation on metadata store.
    $this->metadataStore->expects($this->once())
        ->method('getTransitionMetadata')
        ->with($this->transition)
        ->willReturn([
            'guard' => 'some_expression',
        ]);

    // Create a mock GuardEvaluator that returns true.
    $guardEvaluatorMock = $this->createMock(GuardEvaluator::class);
    $guardEvaluatorMock->expects($this->once())
        ->method('evaluate')
        ->with(
            'some_expression',
            $this->callback(function ($variables) {
                return isset($variables['subject']) && $variables['subject'] === $this->subject;
            })
        )
        ->willReturn(true);

    // Instantiate the subscriber with the mocked GuardEvaluator.
    $subscriber = new WorkflowGuardSubscriber($guardEvaluatorMock, $this->workflowProcessMock);

    // Create the GuardEvent using the shared objects.
    $event = new GuardEvent($this->subject, $this->marking, $this->transition, $this->workflow);

    // Trigger the guard event.
    $subscriber->handleOnGuard($event);

    // Assert that the transition is not blocked.
    expect($event->isBlocked())->toBeFalse();
});

it('allows transition when metadata has no guard key', function () {
    $this->metadataStore->expects($this->once())
        ->method('getTransitionMetadata')
        ->with($this->transition)
        ->willReturn([]); // no 'guard' key

    $guardEvaluatorMock = $this->createMock(GuardEvaluator::class);
    $guardEvaluatorMock->expects($this->never())->method('evaluate');

    $subscriber = new WorkflowGuardSubscriber($guardEvaluatorMock, $this->workflowProcessMock);
    $event = new GuardEvent($this->subject, $this->marking, $this->transition, $this->workflow);

    $subscriber->handleOnGuard($event);

    expect($event->isBlocked())->toBeFalse();
});

it('passes authenticated and user variables to the evaluator', function () {
    $user = Mockery::mock(Authenticatable::class);

    $this->workflowProcessMock = $this->createMock(WorkflowProcess::class);
    $this->workflowProcessMock->method('getAuthenticated')->willReturn(true);
    $this->workflowProcessMock->method('getUser')->willReturn($user);

    $this->metadataStore->expects($this->once())
        ->method('getTransitionMetadata')
        ->willReturn(['guard' => 'authenticated']);

    $guardEvaluatorMock = $this->createMock(GuardEvaluator::class);
    $guardEvaluatorMock->expects($this->once())
        ->method('evaluate')
        ->with(
            'authenticated',
            $this->callback(function ($variables) use ($user) {
                return $variables['authenticated'] === true
                    && $variables['user'] === $user
                    && isset($variables['subject']);
            })
        )
        ->willReturn(true);

    $subscriber = new WorkflowGuardSubscriber($guardEvaluatorMock, $this->workflowProcessMock);
    $event = new GuardEvent($this->subject, $this->marking, $this->transition, $this->workflow);

    $subscriber->handleOnGuard($event);

    expect($event->isBlocked())->toBeFalse();
});
