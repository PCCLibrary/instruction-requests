# Library Instruction System - Documentation Rewrite Implementation Plan

## Overview

**Objective**: Create comprehensive, up-to-date documentation for the Library Instruction Request System that is:
- Brief and focused
- Technically accurate
- Reflects current implementation (as of September 2025)
- Organized for different audiences (users, admins, developers)

**Strategy**: Complete rewrite in stages to avoid session output limits

---

## Documentation Structure

### Primary Documents to Create

1. **README.md** - Project overview and quick links (Stage 1)
2. **user-guide.md** - For faculty and librarians using the system (Stage 2)
3. **admin-guide.md** - For system administrators (Stage 3)
4. **technical-architecture.md** - For developers and LLMs (Stage 4)
5. **deployment-guide.md** - Setup and deployment procedures (Stage 5)

---

## Stage 1: Foundation & Overview

**File**: `README.md`

### Content Outline:
- [ ] Project title and purpose (2-3 sentences)
- [ ] Technology stack summary
- [ ] Quick links to other documentation
- [ ] Current status and version
- [ ] Key features list
- [ ] Getting started (link to deployment guide)
- [ ] Contributing guidelines (if applicable)

### Source Material:
- Implementation plan documents (provided)
- Quick-start guide from Basic-Memory
- Current README.md (if exists)

### Estimated Length: ~200-300 lines

---

## Stage 2: User Guide

**File**: `user-guide.md`

### Content Outline:

#### For Faculty:
- [ ] How to submit instruction requests
- [ ] Field-by-field guidance
- [ ] File upload requirements and process
- [ ] What to expect after submission
- [ ] How to track request status

#### For Librarians:
- [ ] Dashboard overview and navigation
- [ ] Request workflow and statuses explained
- [ ] Assigning and accepting requests
- [ ] Google Calendar integration (user perspective only)
- [ ] File management (uploading/downloading materials)
- [ ] Comment system usage
- [ ] Edit lock system (what users see/experience)

#### For Both:
- [ ] Notification system (what to expect)
- [ ] Common workflows and scenarios
- [ ] User-level troubleshooting only

### Source Material:
- **PRIMARY**: "Instruction Request Form Training Documentation.md" (Claude project files)
- Current docs Section 2 (Application Workflow) - reference only
- Google Calendar integration plan - user-facing aspects only
- Edit lock implementation plan - user experience only

### Approach:
- Use training doc as foundation
- Extract ONLY user-facing content
- Remove all admin/technical details (move to admin/technical docs)
- Keep language simple and user-focused
- Add placeholder notes for screenshots (to be populated after)
- NO links to admin guide or technical docs

### Estimated Length: ~300-400 lines (streamlined from training doc)

---

## Stage 3: Admin Guide

**File**: `admin-guide.md`

### Content Outline:
- [ ] Admin Panel Overview
- [ ] Assignment Availability Management
  - [ ] How the toggle works
  - [ ] When to use it (testing, emergency coverage)
- [ ] Lock Management
  - [ ] Active locks monitoring
  - [ ] Old lock management (1+ hour locks)
  - [ ] Individual vs bulk unlock
  - [ ] Safety considerations
- [ ] Queue Management
  - [ ] Monitoring queue status
  - [ ] Managing failed jobs
  - [ ] Restarting queue workers
- [ ] Cache Management
  - [ ] When to clear caches
  - [ ] Impact of cache clearing
- [ ] System Maintenance
  - [ ] File cleanup procedures
  - [ ] Log management
  - [ ] Database maintenance
- [ ] Troubleshooting Guide
  - [ ] Common issues and solutions
  - [ ] Queue worker problems
  - [ ] Lock system issues
  - [ ] File upload problems
  - [ ] Authentication issues

### Source Material:
- Admin Old Locks Management plan
- Admin User Assignment Availability plan
- "Instruction Request Form Training Documentation.md" - extract admin/technical sections
- Current docs Section 4.7 (Maintenance)
- Deployment guide sections

### Estimated Length: ~400-500 lines

---

## Stage 4: Technical Architecture

**File**: `technical-architecture.md`

### Content Outline:

#### System Architecture:
- [ ] Technology stack details
- [ ] Laravel 11 + Livewire 3 architecture
- [ ] Database schema overview
- [ ] Key models and relationships

#### Core Systems:
- [ ] Edit Lock System
  - [ ] Eloquent Lockable implementation
  - [ ] Custom extensions (locked_by, locked_at)
  - [ ] Stale lock detection logic
  - [ ] JavaScript integration
  - [ ] Scheduled cleanup tasks
- [ ] Notification System
  - [ ] NotificationService architecture
  - [ ] NotificationPackage value object
  - [ ] Email queuing and delivery
  - [ ] Subject line generation logic
  - [ ] Status change triggers
- [ ] File Upload System
  - [ ] Token-based security
  - [ ] Spatie Media Library integration
  - [ ] Storage structure and paths
  - [ ] Temporary file handling
  - [ ] File association flow
- [ ] Google Calendar Integration
  - [ ] Service account authentication
  - [ ] Domain-wide delegation
  - [ ] Event creation/deletion
  - [ ] Attendee management
  - [ ] Error handling
- [ ] Authentication System
  - [ ] Microsoft Entra SAML 2.0
  - [ ] Email-based user lookup
  - [ ] Session management

#### Design Patterns:
- [ ] Repository pattern
- [ ] Service pattern
- [ ] Value object pattern
- [ ] Observer pattern

#### Code Organization:
- [ ] Directory structure
- [ ] Naming conventions
- [ ] Key service classes
- [ ] Livewire components
- [ ] API endpoints

#### Development Standards:
- [ ] Code style (4 spaces, no tabs)
- [ ] PHP 8.2+ requirements
- [ ] Laravel best practices
- [ ] Testing approach

#### Context for LLMs:
- [ ] Project evolution history
- [ ] Key technical decisions
- [ ] Common patterns and anti-patterns
- [ ] Important date boundaries (March 1, 2025 file storage)
- [ ] Multi-tool development workflow

### Source Material:
- All implementation plan documents
- Google Calendar integration technical doc
- Edit Lock implementation plan
- Notification optimization plan
- Entra SAML migration plan
- Current docs Section 4 (Technical Details)
- Quick-start guide

### Estimated Length: ~800-1000 lines

---

## Stage 5: Deployment Guide

**File**: `deployment-guide.md`

### Content Outline:

#### Prerequisites:
- [ ] System requirements
- [ ] Software dependencies
- [ ] Server configuration

#### Installation:
- [ ] Initial setup steps
- [ ] Environment configuration
- [ ] Database setup
- [ ] File permissions

#### Environment-Specific Setup:
- [ ] Local development (.env.local)
- [ ] Test server (.env.testing)
- [ ] Production (.env.production)

#### Deployment Process:
- [ ] Using deploy.sh script
- [ ] Manual deployment steps
- [ ] Post-deployment verification

#### Server Configuration:
- [ ] Directory permissions
- [ ] SELinux configuration
- [ ] Queue worker setup (Supervisor)
- [ ] Scheduled tasks (Cron)

#### Troubleshooting:
- [ ] Permission issues
- [ ] Queue worker problems
- [ ] SELinux issues
- [ ] Cache problems

### Source Material:
- Current docs Section 10 (Deployment)
- Current docs Section 4.6 (System Requirements)
- Deployment script
- Environment files

### Estimated Length: ~300-400 lines

---

## Implementation Strategy

### Session-by-Session Approach:

**Session 1**: Stage 1 - README.md
- Quick foundation document
- Sets structure for everything else
- ~30-45 minutes

**Session 2**: Stage 2 - User Guide
- Adapt training doc for user guide
- Remove admin/technical content
- Add screenshot placeholders
- ~45-60 minutes

**Session 3**: Stage 3 - Admin Guide
- Admin-specific features
- Include admin/technical content from training doc
- ~45-60 minutes

**Session 4**: Stage 4 - Technical Architecture (Part 1)
- System architecture and core systems
- ~60 minutes

**Session 5**: Stage 4 - Technical Architecture (Part 2)
- Design patterns, code organization, LLM context
- ~45-60 minutes

**Session 6**: Stage 5 - Deployment Guide
- Final piece
- References other docs
- ~30-45 minutes

**Session 7**: Review & Polish
- Cross-check all documents
- Ensure consistency
- Verify screenshot placeholders
- Final cleanup
- ~30-45 minutes

---

## Quality Checklist

For each document, ensure:
- [ ] Accurate reflection of current implementation
- [ ] Clear, concise writing
- [ ] Proper markdown formatting
- [ ] Code examples where helpful
- [ ] Screenshot placeholders noted (for user guide)
- [ ] No outdated information
- [ ] Appropriate level of detail for audience
- [ ] No cross-links between user guide and admin/technical docs

---

## Source Material Index

### Implementation Plans (Provided Documents):
1. Google Calendar Integration - Technical Documentation
2. Google Calendar Error Handling - Implementation Plan
3. Notification System Optimization - Implementation Plan
4. Admin User Assignment Availability - Implementation Plan
5. Entra SAML Migration - Implementation Plan
6. Admin Old Locks Management - Implementation Plan v2
7. Library Instruction System Documentation (current/old)
8. **Instruction Request Form Training Documentation.md** (Claude project files)

### Basic-Memory Resources:
- Development Session Quick Start Guide
- Project-specific memories (via Serena)

### Codebase References:
- Model files (InstructionRequests, User, etc.)
- Service files (NotificationService, CalendarService, etc.)
- Configuration files
- Migration files
- Environment files

---

## Success Criteria

Documentation rewrite is complete when:
- [ ] All 5 primary documents are created
- [ ] All checklist items are addressed
- [ ] User guide contains only user-facing content
- [ ] Admin/technical content properly separated
- [ ] Screenshot placeholders identified in user guide
- [ ] Information is accurate and up-to-date
- [ ] Different audiences can find relevant information easily
- [ ] LLM context is preserved for future development
- [ ] Old documentation is archived (already done)

---

## Next Steps

1. Review this implementation plan
2. Confirm approach and structure
3. Begin with Stage 1 (README.md)
4. Progress through stages in order
5. Final review and polish

---

**Created**: September 25, 2025
**Updated**: September 25, 2025
**Status**: Ready for implementation
**Estimated Total Time**: 5-7 hours across 7 sessions
