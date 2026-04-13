-- ============================================================
-- fcl_assets Storage Bucket Setup
-- Safe to re-run: drops policies first before recreating
-- ============================================================

-- STEP 1: Create/update the bucket
INSERT INTO storage.buckets (id, name, public, file_size_limit, allowed_mime_types)
VALUES (
    'fcl_assets',
    'fcl_assets',
    true,
    5242880,
    ARRAY['image/jpeg', 'image/png', 'image/gif', 'image/webp']
)
ON CONFLICT (id) DO UPDATE SET
    public             = EXCLUDED.public,
    file_size_limit    = EXCLUDED.file_size_limit,
    allowed_mime_types = EXCLUDED.allowed_mime_types;

-- STEP 2: Drop existing policies (safe no-op if they don't exist)
DROP POLICY IF EXISTS "Public read access on fcl_assets"    ON storage.objects;
DROP POLICY IF EXISTS "Service role upload on fcl_assets"   ON storage.objects;
DROP POLICY IF EXISTS "Service role update on fcl_assets"   ON storage.objects;
DROP POLICY IF EXISTS "Service role delete on fcl_assets"   ON storage.objects;

-- STEP 3: Recreate policies cleanly
CREATE POLICY "Public read access on fcl_assets"
ON storage.objects FOR SELECT
USING (bucket_id = 'fcl_assets');

CREATE POLICY "Service role upload on fcl_assets"
ON storage.objects FOR INSERT
WITH CHECK (bucket_id = 'fcl_assets');

CREATE POLICY "Service role update on fcl_assets"
ON storage.objects FOR UPDATE
USING (bucket_id = 'fcl_assets');

CREATE POLICY "Service role delete on fcl_assets"
ON storage.objects FOR DELETE
USING (bucket_id = 'fcl_assets');
