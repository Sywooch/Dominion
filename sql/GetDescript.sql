CREATE FUNCTION dominion_db.GetShortDescript(ItemId int)
  RETURNS text CHARSET utf8
  SQL SECURITY INVOKER
BEGIN
  DECLARE attr_name,
          attr_value,
          buf_value varchar(150);
  DECLARE v_finished integer DEFAULT 0;
  DECLARE descript text DEFAULT '';
  DECLARE i int(11) DEFAULT 0;


  DECLARE attr_cursor CURSOR FOR
  SELECT
    A.`NAME`,
    AL.`NAME`
  FROM ITEM I
    JOIN ITEM0 I1 USING (ITEM_ID)
    JOIN ATTRIBUT A USING (ATTRIBUT_ID)
    JOIN ATTRIBUT_LIST AL USING (ATTRIBUT_ID)
  WHERE I1.VALUE = AL.ATTRIBUT_LIST_ID
  AND I.ITEM_ID = ItemId;

  DECLARE attr_cursor1 CURSOR FOR
  SELECT
    A.`NAME`,
    I0.VALUE
  FROM ITEM I
    JOIN ITEM0 I0 USING (ITEM_ID)
    JOIN ATTRIBUT A USING (ATTRIBUT_ID)
  WHERE A.TYPE = 0
  AND I.ITEM_ID = ItemId;

  DECLARE CONTINUE HANDLER FOR SQLSTATE '23000' SET v_finished = 1;

  DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET v_finished = 1;

  OPEN attr_cursor;
lbl:
LOOP

  FETCH attr_cursor INTO attr_name, attr_value;
  IF v_finished = 1 THEN
    LEAVE lbl;
  ELSEIF i <> 0 THEN
    SET descript = CONCAT(descript, "<i class=\"dvdr\">/</i> ");
  END IF;


  SET descript = CONCAT(descript, attr_name, ": <b>", attr_value, "</b> ");

  SET i = i + 1;

END LOOP;
  CLOSE attr_cursor;

  SET v_finished = 0;
  SET i = 0;
  OPEN attr_cursor1;
lbl:
LOOP

  IF v_finished = 1 THEN
    LEAVE lbl;
  ELSEIF i <> 0 THEN
    SET descript = CONCAT(descript, "<i class=\"dvdr\">/</i> ");
  END IF;

  SET descript = CONCAT(descript, attr_name, ": <b>", attr_value, "</b> ");
  SET i = i + 1;

END LOOP;
  CLOSE attr_cursor1;

  RETURN descript;
END
