-- =====================================
-- CONGO CONNECT - INITIAL DATA
-- =====================================

-- ROLES
INSERT INTO roles (role_key, role_name) VALUES
('member', 'Member'),
('professional', 'Professional'),
('company', 'Company'),
('association', 'Association'),
('admin', 'Admin'),
('super_admin', 'Super Admin');

-- MODULES
INSERT INTO platform_modules (module_key, module_name) VALUES
('profiles', 'Profiles'),
('companies', 'Companies'),
('associations', 'Associations'),
('jobs', 'Jobs'),
('events', 'Events'),
('articles', 'Articles'),
('feed', 'News Feed'),
('media', 'Media'),
('messaging', 'Messaging'),
('chat', 'Chat'),
('subscriptions', 'Subscriptions'),
('payments', 'Payments'),
('admin', 'Admin'),
('ai', 'Artificial Intelligence');

-- ACTIONS
INSERT INTO module_actions (module_id, action_key, action_name)
SELECT id, 'view', 'View' FROM platform_modules;

INSERT INTO module_actions (module_id, action_key, action_name)
SELECT id, 'create', 'Create' FROM platform_modules;

INSERT INTO module_actions (module_id, action_key, action_name)
SELECT id, 'edit', 'Edit' FROM platform_modules;

INSERT INTO module_actions (module_id, action_key, action_name)
SELECT id, 'delete', 'Delete' FROM platform_modules;

-- SUBSCRIPTION PLANS
INSERT INTO subscription_plans (name, slug, price, duration_value, duration_unit) VALUES
('Free', 'free', 0.00, 1, 'month'),
('Premium', 'premium', 9.99, 1, 'month'),
('Business', 'business', 29.99, 1, 'month');

-- BASIC PERMISSIONS (example)
INSERT INTO plan_permissions (plan_id, module_id, action_id, is_allowed, quota_value, quota_period)
SELECT 1, pm.id, ma.id, 1, 10, 'month'
FROM platform_modules pm
JOIN module_actions ma ON ma.module_id = pm.id
WHERE pm.module_key = 'messaging' AND ma.action_key = 'create';

INSERT INTO plan_permissions (plan_id, module_id, action_id, is_allowed, quota_value, quota_period)
SELECT 2, pm.id, ma.id, 1, NULL, NULL
FROM platform_modules pm
JOIN module_actions ma ON ma.module_id = pm.id
WHERE pm.module_key = 'messaging' AND ma.action_key = 'create';