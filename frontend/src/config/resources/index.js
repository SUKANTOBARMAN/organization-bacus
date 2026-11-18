import notices from './notices'
import posts from './posts'
import documents from './documents'
import pages from './pages'
import albums from './albums'
import sliders from './sliders'
import videos from './videos'
import designations from './designations'
import members from './members'
import committees from './committees'
import committeeMembers from './committee-members'
import commissionMembers from './commission-members'
import elections from './elections'
import electionCandidates from './election-candidates'
import users from './users'
import activityLogs from './activity-logs'

const ADMINS = ['super_admin', 'admin']
const withRoles = (cfg, roles) => ({ ...cfg, roles: cfg.roles || roles })

export const resources = Object.fromEntries(
  [
    notices, posts, documents, pages, albums, sliders, videos,
    withRoles(designations, ADMINS), withRoles(members, ADMINS), withRoles(committees, ADMINS),
    withRoles(committeeMembers, ADMINS), withRoles(commissionMembers, ADMINS),
    withRoles(elections, ADMINS), withRoles(electionCandidates, ADMINS), users, activityLogs,
  ].map((r) => [r.key, r]),
)

// নিজস্ব পেজ আছে এমন রিসোর্স (nested নয়)
export const standaloneResources = Object.values(resources).filter((r) => !r.nested)
