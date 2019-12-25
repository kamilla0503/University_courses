CREATE PROCEDURE `delete_on_dp` (d INT)
BEGIN
 DELETE FROM Sample_variant WHERE Sample_variant.DP=d;
END
