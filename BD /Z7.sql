CREATE PROCEDURE `variant_in_population` ()
BEGIN
SELECT id_Variant, Population_Population_name as population, max_freq as max_frequency FROM (SELECT Variant_idVariant as id_Variant, max(Frequency) as max_freq  FROM Population_variant GROUP BY Variant_idVariant) AS max_inf JOIN Population_variant ON max_inf.max_freq =  Population_variant.Frequency and max_inf.id_Variant= Population_variant. Variant_idVariant ;
END