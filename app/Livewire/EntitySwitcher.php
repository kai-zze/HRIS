<?php

namespace App\Livewire;

use App\Models\Entity;
use App\Services\EntityContextService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class EntitySwitcher extends Component
{
    public string $entityId = '';

    public function mount(EntityContextService $context): void
    {
        $activeId = $context->activeEntityId();
        $this->entityId = ($activeId && $activeId > 0) ? (string) $activeId : '';
    }

    public function updatedEntityId(string $value, EntityContextService $context): void
    {
        if (! $context->canSwitchEntities()) {
            return;
        }

        $parsedId = $value !== '' ? (int) $value : null;
        $context->setActiveEntity($parsedId);

        $target = request()->header('Referer') ?: url('/admin');
        $this->redirect($target, navigate: true);
    }

    /**
     * @return Collection<int, Entity>
     */
    public function getEntitiesProperty(): Collection
    {
        return Entity::query()
            ->where('is_active', true)
            ->orderBy('id')
            ->get(['id', 'code', 'short_name', 'name', 'logo_path']);
    }

    public function render(EntityContextService $context): View
    {
        $user = Auth::user();
        $canSwitch = $context->canSwitchEntities($user);
        $lockedEntity = ! $canSwitch && $user?->entity_id
            ? Entity::query()->find($user->entity_id, ['id', 'code', 'short_name', 'name', 'logo_path'])
            : null;
        $activeEntity = $canSwitch && $this->entityId !== ''
            ? $this->entities->firstWhere('id', (int) $this->entityId)
            : $lockedEntity;

        return view('livewire.entity-switcher', [
            'canSwitch' => $canSwitch,
            'lockedEntity' => $lockedEntity,
            'activeEntity' => $activeEntity,
            'entities' => $canSwitch ? $this->entities : collect(),
        ]);
    }
}
