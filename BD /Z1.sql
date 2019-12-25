CREATE DEFINER=`root`@`localhost` PROCEDURE `ins_sample`(i INT, M VARCHAR(10), P VARCHAR(45))
BEGIN
insert into Sample(id, Method, Population_Population_name ) values(i, M, P); 
END //

DELIMER;