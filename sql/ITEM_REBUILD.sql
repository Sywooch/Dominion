CREATE TRIGGER ITEM_REBUILD
	AFTER UPDATE
	ON ITEM0
	FOR EACH ROW
BEGIN

  UPDATE ITEM
  SET DESCRIPTION = GetShortDescript(OLD.ITEM_ID)
  WHERE ITEM_ID = OLD.ITEM_ID;
  
END