# Project Package Conventions

## Searchable Select (`williamug/searchable-select`)

Use `<x-searchable-select>` for **all select/dropdown fields** in Livewire components. Do not use `<flux:select>` or native `<select>` elements.

### Required props

| Prop | Description |
|------|-------------|
| `:options` | Eloquent collection or array of options |
| `wire-model` | Livewire property name as a **string** (kebab-case attribute, not `wire:model`) |
| `:selected-value` | Current property value for correct initial display |
| `placeholder` | Placeholder text shown when nothing is selected |

### Field name defaults

By default the component reads `id` as the option value and `name` as the displayed label. Override with `option-value` and `option-label` when the model uses different field names.

### Usage example

```blade
<x-searchable-select
    :options="$this->categories"
    wire-model="categoryId"
    :selected-value="$categoryId"
    placeholder="Select a category"
/>
```

### Wrapping with flux:field

Wrap in `<flux:field>` to get consistent label/error layout alongside other Flux inputs:

```blade
<flux:field>
    <flux:label>Category *</flux:label>
    <x-searchable-select
        :options="$this->categories"
        wire-model="categoryId"
        :selected-value="$categoryId"
        placeholder="Select a category"
    />
    <flux:error name="categoryId" />
</flux:field>
```
