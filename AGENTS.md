<!-- BEGIN:nextjs-agent-rules -->

# This is NOT the Next.js you know

This version has breaking changes — APIs, conventions, and file structure may all differ from your training data. Read the relevant guide in `node_modules/next/dist/docs/` (resolved from this file's directory; in monorepos the `next` package may not be visible from the repo root) before writing any code. Heed deprecation notices.

This block is written and re-added by `next dev` — verify at `node_modules/next/dist/server/lib/generate-agent-files.js`. Removing it from a diff only re-creates the uncommitted change; committing it with your work keeps the tree clean.

<!-- END:nextjs-agent-rules -->

# Agent Instructions

## Project

Software agency website built with Next.js 16.3.1, React 19, TypeScript 5, Tailwind CSS 4, and shadcn/ui (radix-nova style).

## Commands

```bash
npm run dev      # Start dev server (http://localhost:3000)
npm run build    # Production build
npm run start    # Start production server
npm run lint     # ESLint (flat config)
```

## Structure

```
src/
  app/           # Next.js App Router (page.tsx, layout.tsx, globals.css)
  components/ui/ # shadcn/ui components
  hooks/         # Custom React hooks
  lib/           # Utility functions (utils.ts)
```

## Key Details

- **Path aliases**: `@/*` maps to `./src/*`
- **React Compiler**: Enabled in next.config.ts
- **UI Components**: shadcn/ui with `radix-nova` style, Lucide icons
- **Styling**: Tailwind CSS 4 with CSS variables (oklch color format)
- **Form handling**: React Hook Form + resolvers
- **Data fetching**: TanStack React Query
- **Notifications**: sonner
- **Theming**: next-themes (dark mode via `.dark` class)
