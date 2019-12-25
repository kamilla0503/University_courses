
CREATE DEFINER=`root`@`localhost` PROCEDURE `ins_variant` (i VARCHAR(15), c VARCHAR(2), p VARCHAR(45) )
BEGIN
insert into Variant(idVariant, Chromosome, Posiion) values(i, c, p);
END //

DELIMER;