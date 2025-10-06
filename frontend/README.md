# Better Boss Frontend README

## Table of Contents

1. [Stack](#stack)
2. [Coding Standards](#coding-standards)
   - [TypeScript](#typescript)
   - [Naming](#naming)
3. [Project Structure](#project-structure)
4. [API Layer](#api-layer)
5. [Components](#components)
   - [Props & Emits](#props--emits)
   - [Loading, Empty, & Error States](#loading-empty--error-states)
6. [State (Pinia)](#state-pinia)

## Stack

- Nuxt `4.0.3`
- TypeScript `5.9.x`
- Pinia `3.0.3`
- PrimeVue `4.3.7`
- Prime Icons
- PrimeUIX Themes
- TailwindCSS

## Coding Standards

### TypeScript

- Strict mode on. No `any` in PRs unless justified
- Public interfaces/types live in `app/api/types` or near usage (typically in the relevant API file) with clear names.
- Types consistent with backend objects will be expected to have attributes in snake case as this is what the backend will send.

### Naming

- Files: `kebab-case.vue` for components; directories also kebab-case.
- Components (auto-import): PascalCase in templates (e.g., `HomeUsersTable`).

## Project Structure

We use Nuxt 4's Project Structure
```
/
├─ app/
│  ├─ api/              # frontend API layer (BaseApi, APIs)
│  ├─ assets/           # global assets (css, images)
│  ├─ components/       # global components
│  ├─ layouts/          # application layouts
│  ├─ middleware/       # route middleware (named)
│  ├─ pages/            # file-based routing
│  ├─ plugins/          # nuxt plugins (client/server)
│  ├─ stores/           # pinia stores
│  ├─ utils/            # small utilities/helpers
│  ├─ services/         # cross-cutting services (ApiService, toast, etc.)
│  ├─ app.config.ts     # app-level config for injection/useAppConfig()
│  └─ app.vue           # root
├─ nuxt.config.ts
├─ tailwind.config.ts
└─ tsconfig.json
```
#### Aliases

`~` → project root, `/app` is the main source folder

## API Layer

All domain APIs extend `BaseApi<TModel, TFilter>` and use `ApiService` to talk to the backend.


**Example**

```typescript
export class ExampleAPI extends BaseApi<TModel, TFilter> {
    // Inherit base functions with route for this class
    constructor() {
        super('route');
    }
    
    // Add additional route functions
    async getAdditionalExample(): Promise<TModel | null> {
        return this.unwrap<TModel>(
            apiService.get('route')
        )
    }
}
```

## Components

- **Options API** is preferred (team convention).
- File order: `<script lang="ts">`, `template`, `<style scoped>`.
- Keep components focused; prefer props/emits over global store.
- **Auto-imports**: Nuxt 4 auto-registers anything under `app/components/**`. You don’t need manual imports—just use the component tag, and Nuxt will resolve it.

| File path (under `app/components`) | Auto component name    |
|------------------------------------| ---------------------- |
| `button.vue`                       | `Button`               |
| `home/users/table.vue`             | `HomeUsersTable`       |
| `home/users/index.vue`             | `HomeUsers`            |
| `stats/user-card.vue`              | `StatsUserCard`        |

### Props & Emits

- Use explicit interfaces for props in TS.
- Emit names are kebab-case (`update:model-value`), but typed in TS.

### Loading, Empty, & Error States

All components must gracefully handle three states:

1. Loading: Show Skeletons that match the final layout dimensions
2. Empty: Fall back or display explicit "no data" state
   - Use sensible defaults in templates and scripts: {{ value ?? '—' }}
3. Error: Message + retry
   - Surface a concise error toast and render an inline retry when appropriate.
   - Avoid blank screens; prefer an inline error block that preserves layout.

## State (Pinia)

- One store per domain (`stores/inventory.ts`, `stores/user.ts`).
- No cross-store mutations—compose via actions/getters.
- Persist only when necessary, not entire stores.