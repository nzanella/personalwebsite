/* World.java        
 * 
 * This file is part of a set of files used to code a game
 * of Pac Man on an Android mobile device. It represents
 * the world where our main character, ghosts, and edible
 * tokens live and interact.
 * 
 * Copyright (c) Neil Zanella. All rights reserved. */

package com.neilzanella.pac;

import java.io.BufferedReader;
import java.io.BufferedWriter;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.OutputStream;
import java.io.OutputStreamWriter;
import java.nio.channels.FileChannel;
import java.util.ArrayList;
import java.util.Collections;
import java.util.List;
import java.util.Random;

import android.content.Context;
import android.os.Handler;
import android.os.Message;

class World {

  static final int pieceWidth = 14; // ideal piece width in pixels
  static final int pieceHeight = 14; // ideal piece height in pixels

  static final int DIR_LEFT = 0;
  static final int DIR_RIGHT = 1;
  static final int DIR_UP = 2;
  static final int DIR_DOWN = 3;

  static final int INIT_NUM_LIVES = 3;

  static final int PILL_POINTS = 10;

  static final int POWER_POINTS = 50;

  static final int GHOST_POINTS = 100;

  static final int NUM_LIFE_POINTS = 2000;

  private static float collisionEpsilon = 5.0f;

  Context context;

  static int numWorlds = 2;

  static int worldNum = 0;

  PacPiece pacPiece = null;

  List<GhostPiece> ghostPieces = new ArrayList<GhostPiece>();

  List<Boolean> ghostIsColliding;

  Handler worldHandler;

  WorldState worldState;

  int[][] distances = new int[mazeHeight][mazeWidth];

  int numLives = INIT_NUM_LIVES;

  int numPoints = 0;

  int numResidualPoints = 0;

  int level = 1;

  int presPowerBlinkState = 0;

  int numPresPowerBlinkStates = 2;

  int presGhostBlinkState = 0;

  int numPresGhostBlinkStates = 2;

  static float SPEED_ESCAPE = 20.0f;

  static float SPEED_NORMAL = 30.0f;

  static float SPEED_CAUGHT = 70.0f;

  static float SPEED_LEVEL_INCR = 10.0f;

  static Random random = new Random();

  static boolean isPaused = false;

  GameActivity.Panel panel;
  
  public World(GameActivity.Panel panel) {

    this.panel = panel;

    this.context = panel.context;

    init();

    readEmptyMaze();

  }

  public World(GameActivity.Panel panel, int level) {

    this.panel = panel;

    this.context = panel.context;

    init();

    worldNum = level;

    readMaze();

  }

  public void init() {

    ghostIsColliding = new ArrayList<Boolean>();

    for (int k = 0; k < ghostPieces.size(); k++)

      ghostIsColliding.add(new Boolean(false));

    initState(WorldStateSleeping.instance());

    setNumWorlds();

    System.out.println("numWorlds:" + numWorlds);

    initMazes();

  }

  public void run() {

    initState(WorldStateStarting.instance());

  }

  private void initState(WorldState worldState) {

    worldHandler = new WorldHandler(this);

    this.worldState = worldState;

    this.worldState.enter(this);

  }

  void setState(WorldState worldState) {

    this.worldState.exit(this);

    this.worldState = worldState;

    this.worldState.enter(this);

  }

  public void reset() {

    pacPiece.init();
    
    pacPiece.setSpeed(SPEED_NORMAL);

    for (int k = 0; k < ghostPieces.size(); k++) {

      ghostPieces.get(k).init();
    
      ghostPieces.get(k).setSpeed(SPEED_NORMAL);
    
    }

  }
  
  public void repopulate() {
    
    for (int i = 0; i < mazeHeight; i++)
      
      for (int j = 0; j < mazeWidth; j++)
        
        if (mazeMap[i][j]  == symbolPill)
            
          ((MazeSpace) mazePieces[i][j]).type = MazeSpace.PILL_REGULAR;
        
        else if (mazeMap[i][j] == symbolPower)
       
          ((MazeSpace) mazePieces[i][j]).type = MazeSpace.PILL_POWER;
        
  }
  
  public void incrLevel() {
    
    ++level;
    
    SPEED_ESCAPE += SPEED_LEVEL_INCR;

    SPEED_NORMAL += SPEED_LEVEL_INCR;

    SPEED_CAUGHT += SPEED_LEVEL_INCR;
    
  }

  public void update(float deltaTime) {

    if (!this.isPaused)

      worldState.update(this, deltaTime);

  }

  void pause() {

    TimeStampedMessage.pauseTimers();

  }

  void resume() {

    TimeStampedMessage.resumeTimers();

  }

  void increaseScore(int numPoints) {

    this.numPoints += numPoints;

    this.numResidualPoints += numPoints;

    if (this.numResidualPoints >= NUM_LIFE_POINTS) {

      ++numLives;

      this.numResidualPoints = NUM_LIFE_POINTS - this.numResidualPoints;

    }

  }

  public void checkCollisions() {

    if (pacPiece != null)

      for (int k = 0; k < ghostPieces.size(); k++) {

        int squareDistA = (ghostPieces.get(k).pacLocX - pacPiece.pacLocX) * (ghostPieces.get(k).pacLocX - pacPiece.pacLocX);
        int squareDistB = (ghostPieces.get(k).pacLocY - pacPiece.pacLocY) * (ghostPieces.get(k).pacLocY - pacPiece.pacLocY);

        ghostIsColliding.set(k, Math.sqrt(squareDistA + squareDistB) < collisionEpsilon);

      }

  }

  private void regularPillEaten() {
    
    if (panel.soundsEnabled)

      panel.soundPool.play(panel.soundGamePillEaten, 0.5f, 0.5f, 0, 0, 1.0f);

    increaseScore(PILL_POINTS);
    
  }

  private void powerPillEaten() {

    increaseScore(POWER_POINTS);

    if (panel.soundsEnabled)

      panel.soundPool.play(panel.soundGamePowerEaten, 1.0f, 1.0f, 0, 0, 1.0f);

    worldState.sendMessage(this, WorldStateRunning.WORLD_GHOST_ESCAPE_TIMER, World.WorldHandler.MESSAGE_WHAT_GHOST_ESCAPE_TIMER);

    worldState.sendMessage(this, WorldStateRunning.WORLD_PRES_GHOST_ESCAPE_BLINK_UPDATE_TIME, World.WorldHandler.MESSAGE_WHAT_PRESENTATION_GHOST_ESCAPE);

    for (int k = 0; k < ghostPieces.size(); k++) {

      ghostPieces.get(k).directionEvent = ghostPieces.get(k).getReverseDirection();

      // ensure ghost does not get stuck right in front of a wall when reversing by forcing direction recomputation

      ghostPieces.get(k).setMode(GhostModeEscape.instance());

    }
    
  }
  
  void checkWinning() {
    
    System.out.println("Check winning called.");
  
    for (int i = 0; i < mazeHeight; i++)
      
      for (int j = 0; j < mazeWidth; j++)
        
        if (mazePieces[i][j] instanceof MazeSpace && (((MazeSpace) mazePieces[i][j]).type == MazeSpace.PILL_REGULAR || ((MazeSpace) mazePieces[i][j]).type == MazeSpace.PILL_POWER))
        
          return;
          
    setState(WorldStateWinning.instance());

  }

  class WorldHandler extends Handler {

    WorldHandler(World world) {

      this.world = world;

    }

    @Override
    public void handleMessage(Message msg) {

      TimeStampedMessage.unloadMessages(msg.getTarget(), msg.what);

      world.worldState.timerExpired(world, msg);

    }

    public static final int MESSAGE_WHAT_WORLD_UPDATE = 0;

    public static final int MESSAGE_WHAT_GHOST_ESCAPE_TIMER = 1;

    public static final int MESSAGE_WHAT_PRESENTATION_GHOST_ESCAPE = 2;

    public static final int MESSAGE_WHAT_PRESENTATION_POWER = 3;

    private World world;

  }

  class GhostHandler extends Handler {

    GhostHandler(GhostPiece ghostPiece) {

      this.ghostPiece = ghostPiece;

    }

    private GhostPiece ghostPiece;

    @Override
    public void handleMessage(Message msg) {

      ghostPiece.ghostMode.timerExpired(ghostPiece);

    }

    public static final int MESSAGE_WHAT_GHOST_MODE = 0;

  }

  class PacPiece extends MovingPiece {

    int presMouthPosition = -1;

    static final int presNumMouthPositions = 4;

    // microseconds till next presentation change

    static final int PRES_CHANGE_TIME = 50;

    public PacPiece(int pacLocX, int pacLocY) {

      homeLocX = pacLocX;

      homeLocY = pacLocY;

      init();

    }

    void init() {

      super.initPacLoc(homeLocX, homeLocY);

      directionEvent = pacNextDirection = pacDirection = DIR_LEFT;

    }

    public void update(float deltaTime) {

      move(deltaTime);

      if (locationChanged())

        presUpdateMouthPosition();

    }

    public void presUpdateMouthPosition() {

      presMouthPosition = ++presMouthPosition % presNumMouthPositions;

    }

    public void presResetMouthPosition() {

      presMouthPosition = -1;

    }

  }

  class GhostPiece extends MovingPiece {

    public World world;

    public GhostHandler ghostHandler;

    public int[][] distances = new int[World.mazeHeight][World.mazeWidth];

    public int[][] currentDistances = distances;

    public int presSkirtPosition = 0;

    public static final int numPresSkirtPositions = 2;

    public GhostPiece(World world, int ghostType, int pacLocX, int pacLocY) {

      this.world = world;

      this.ghostType = ghostType;

      initMode(GhostModeSleeping.instance());

      super.initPacLoc(pacLocX, pacLocY);

      directionEvent = pacNextDirection = pacDirection = DIR_UP;

      homeLocX = pacLocX;

      homeLocY = pacLocY;

    }

    public void init() {

      super.initPacLoc(homeLocX, homeLocY);

      directionEvent = pacNextDirection = pacDirection = DIR_UP;

      setMode(GhostModeScatter.instance());

    }

    public void update(float deltaTime) {

      this.ghostMode.update(this, deltaTime);

    }

    public boolean isHome() {

      int pacPieceLocX = pacLocX / pieceWidth;
      int pacPieceLocY = pacLocY / pieceHeight;
      int homePieceLocX = homeLocX / pieceWidth;
      int homePieceLocY = homeLocY / pieceHeight;

      return pacPieceLocX == homePieceLocX && pacPieceLocY == homePieceLocY;

    }

    public void directionChanged() {

    }

    private void initMode(GhostMode ghostMode) {

      ghostHandler = new GhostHandler(this);

      this.ghostMode = ghostMode;

      this.ghostMode.enter(this);

    }

    void setMode(GhostMode ghostMode) {

      this.ghostMode.exit(this);

      this.ghostMode = ghostMode;

      this.ghostMode.enter(this);

    }
    
    boolean pieceIsStuck() {
      
      return getNextForkType() == FORK_BLIND && getMaxLinearDeltaDisp() == 0;
      
    }
    
    protected void setNextClosestDirection() {
      
      // Random random = new Random();

      System.out.println("pacPieceLocX: " + pacLocX / pieceWidth);
      System.out.println("pacPieceLocY: " + pacLocY / pieceHeight);
      System.out.println("Next fork type: " + getNextForkType());
      System.out.println("Next max linear delta disp: " + getMaxLinearDeltaDisp());

      switch (getNextForkType()) {

        case FORK_BLIND:

          if (getMaxLinearDeltaDisp() == 0) {
            
            System.out.println("Stuck in a blind fork!");
            
            // ensure we don't get stuck against a wall

            directionEvent = getReverseDirection();
            
          }
          
          break;

        case FORK_NONE:

          System.out.println("No fork!");
            
          break;

        case FORK_LEFT:

          directionEvent = getLeftDirection();

          break;

        case FORK_RIGHT:

          directionEvent = getRightDirection();

          break;

        case FORK_LEFT_RIGHT: {

          // System.out.println("Encountered left right fork...");

          int leftPieceLocX = getLeftTurnX(nextForkX);
          int leftPieceLocY = getLeftTurnY(nextForkY);

          int rightPieceLocX = getRightTurnX(nextForkX);
          int rightPieceLocY = getRightTurnY(nextForkY);

          int leftDistance = currentDistances[leftPieceLocY][leftPieceLocX];

          int rightDistance = currentDistances[rightPieceLocY][rightPieceLocX];

          if (leftDistance < rightDistance)

            directionEvent = getLeftDirection();

          else if (rightDistance < leftDistance)

            directionEvent = getRightDirection();

          else {

            switch (random.nextInt(2)) {

              case 0:

                directionEvent = getLeftDirection();

                break;

              case 1:

                directionEvent = getRightDirection();

                break;

            }

          }

          break;

        }

        case FORK_LEFT_FORWARD: {

          // System.out.println("Encountered left forward fork...");

          int leftPieceLocX = getLeftTurnX(nextForkX);
          int leftPieceLocY = getLeftTurnY(nextForkY);

          int forwardPieceLocX = getForwardTurnX(nextForkX);
          int forwardPieceLocY = getForwardTurnY(nextForkY);

          int leftDistance = currentDistances[leftPieceLocY][leftPieceLocX];

          int forwardDistance = currentDistances[forwardPieceLocY][forwardPieceLocX];

          if (leftDistance < forwardDistance)

            directionEvent = getLeftDirection();

          else if (forwardDistance < leftDistance)

            directionEvent = pacDirection;

          else {

            switch (random.nextInt(2)) {

              case 0:

                directionEvent = getLeftDirection();

                break;

              case 1:

                directionEvent = pacDirection;

                break;

            }

          }

          break;

        }

        case FORK_RIGHT_FORWARD: {

          // System.out.println("Encountered right forward fork...");

          int rightPieceLocX = getRightTurnX(nextForkX);
          int rightPieceLocY = getRightTurnY(nextForkY);

          int forwardPieceLocX = getForwardTurnX(nextForkX);
          int forwardPieceLocY = getForwardTurnY(nextForkY);

          int rightDistance = currentDistances[rightPieceLocY][rightPieceLocX];

          int forwardDistance = currentDistances[forwardPieceLocY][forwardPieceLocX];

          if (rightDistance < forwardDistance)

            directionEvent = getRightDirection();

          else if (forwardDistance < rightDistance)

            directionEvent = pacDirection;

          else {

            switch (random.nextInt(2)) {

              case 0:

                directionEvent = getRightDirection();

                break;

              case 1:

                directionEvent = pacDirection;

                break;

            }

          }

          break;

        }

        case FORK_LEFT_RIGHT_FORWARD: {

          // System.out.println("Encountered left right forward fork...");

          int leftPieceLocX = getLeftTurnX(nextForkX);
          int leftPieceLocY = getLeftTurnY(nextForkY);

          int rightPieceLocX = getRightTurnX(nextForkX);
          int rightPieceLocY = getRightTurnY(nextForkY);

          int forwardPieceLocX = getForwardTurnX(nextForkX);
          int forwardPieceLocY = getForwardTurnY(nextForkY);

          int leftDistance = currentDistances[leftPieceLocY][leftPieceLocX];
          int rightDistance = currentDistances[rightPieceLocY][rightPieceLocX];
          int forwardDistance = currentDistances[forwardPieceLocY][forwardPieceLocX];

          List<Integer> values = new ArrayList<Integer>();
          values.add(leftDistance);
          values.add(rightDistance);
          values.add(forwardDistance);

          IntegerListFuncs retriever = new IntegerListFuncs(values);
          int index = retriever.getRandMinIndex();

          switch (index) {

            case 0:

              directionEvent = leftDistance;

              break;

            case 1:

              directionEvent = rightDistance;

              break;

            case 2:

              directionEvent = pacDirection;

              break;

          }

        }

      }

    }

    public void setNextRandDirection() {

      switch (getNextForkType()) {

        case FORK_BLIND:

          if (getMaxLinearDeltaDisp() == 0) {
            
            directionEvent = getReverseDirection();
            
          }

          break;

        case FORK_NONE:

          break;

        case FORK_LEFT:

          directionEvent = getLeftDirection();

          break;

        case FORK_RIGHT:

          directionEvent = getRightDirection();

          break;

        case FORK_LEFT_RIGHT:

          switch (random.nextInt(2)) {

            case 0:

              directionEvent = getLeftDirection();

              break;

            case 1:

              directionEvent = getRightDirection();

              break;

          }

          break;

        case FORK_LEFT_FORWARD:

          switch (random.nextInt(2)) {

            case 0:

              directionEvent = getLeftDirection();

              break;

            case 1:

              break;

          }

          break;

        case FORK_RIGHT_FORWARD:

          switch (random.nextInt(2)) {

            case 0:

              directionEvent = getRightDirection();

              break;

            case 1:

              break;

          }

          break;

        case FORK_LEFT_RIGHT_FORWARD:

          switch (random.nextInt(3)) {

            case 0:

              directionEvent = getLeftDirection();

              break;

            case 1:

              directionEvent = getRightDirection();

              break;

            case 2:

              break;

          }

          break;

      }

    }

    void presUpdateSkirtPosition() {

      presSkirtPosition = ++presSkirtPosition % numPresSkirtPositions;

    }

    int ghostType;

    GhostMode ghostMode;

    static final int TYPE_BLINKY = 0;
    static final int TYPE_PINKY = 1;
    static final int TYPE_INKY = 2;
    static final int TYPE_CLYDE = 3;

  }

  abstract class MovingPiece {
    
    public int directionEvent;

    int pacDirection, pacNextDirection;

    public int pacLocX, pacLocY, lastPacLocX, lastPacLocY; // pacman location in pixels

    public int homeLocX, homeLocY;

    private int currentPacPieceLocX = -1, currentPacPieceLocY = -1;

    Random random = new Random(System.currentTimeMillis() + World.random.nextLong());

    public void initPacLoc(int pacLocX, int pacLocY) {

      this.pacLocX = this.lastPacLocX = pacLocX;
      this.pacLocY = this.lastPacLocY = pacLocY;

    }

    public boolean pieceLocationChanged() {

      int pacPieceLocX = pacLocX / pieceWidth;
      int pacPieceLocY = pacLocY / pieceHeight;

      boolean changed = currentPacPieceLocX != pacPieceLocX || currentPacPieceLocY != pacPieceLocY;

      currentPacPieceLocX = pacPieceLocX;
      currentPacPieceLocY = pacPieceLocY;

      return changed;

    }

    public boolean locationChanged() {

      return pacLocX != lastPacLocX || pacLocY != lastPacLocY;

    }

    private void setPacLocX(int pacLocX) {

      this.lastPacLocX = this.pacLocX;
      this.pacLocX = pacLocX;

      // eat pills between current and last position

      switch (pacDirection) {

        case DIR_RIGHT:

          strideX(this.lastPacLocX, this.pacLocX);

          break;

        case DIR_LEFT:

          strideX(this.pacLocX, this.lastPacLocX);

          break;

        default:

          System.out.println("Logic error.");
      }

    }

    private void setPacLocY(int pacLocY) {

      this.lastPacLocY = this.pacLocY;
      this.pacLocY = pacLocY;

      // eat pills between current and last position

      if (this.lastPacLocY < this.pacLocY)

        strideY(this.lastPacLocY, this.pacLocY);

      else

        strideY(this.pacLocY, this.lastPacLocY);

    }

    private void strideX(int fromPacLocX, int toPacLocX) {

      if (fromPacLocX > toPacLocX)
        return;

      int fromPacPieceLocX = fromPacLocX / pieceWidth;

      int toPacPieceLocX = toPacLocX / pieceWidth;

      int pacPieceLocY = pacLocY / pieceHeight;

      for (int pieceLocX = fromPacPieceLocX; pieceLocX <= toPacPieceLocX; pieceLocX = (pieceLocX + 1) % mazeWidth) {

        int foodLocX = pieceLocX * pieceWidth + pieceWidth / 2;

        if (fromPacLocX <= foodLocX && foodLocX <= toPacLocX) {

          // check just in case

          if (this instanceof PacPiece && mazePieces[pacPieceLocY][pieceLocX] instanceof MazeSpace) {

            if (((MazeSpace) mazePieces[pacPieceLocY][pieceLocX]).type == MazeSpace.PILL_POWER) {

              powerPillEaten();

            } else if (((MazeSpace) mazePieces[pacPieceLocY][pieceLocX]).type == MazeSpace.PILL_REGULAR) {

              regularPillEaten();

            }

            ((MazeSpace) mazePieces[pacPieceLocY][pieceLocX]).type = MazeSpace.BLANK;
            
            checkWinning();

          }

          else if (this instanceof GhostPiece && ((GhostPiece) this).ghostMode instanceof GhostModeCaught
              && pacPieceLocY == homeLocY / pieceHeight && pieceLocX == homeLocX / pieceWidth) {

            System.out.println("Reached original enemy coordinates.");

            ((GhostPiece) this).setMode(GhostModeScatter.instance());

          }

        }

        // ensure we exit loop even in case we're at the boundary of the maze

        if (pieceLocX == toPacPieceLocX)

          break;

      }

    }

    class PieceLocation {

      public PieceLocation(int pieceLocX, int pieceLocY) {

        setLocation(pieceLocX, pieceLocY);

      }

      public void setLocation(int pieceLocX, int pieceLocY) {

        this.pieceLocX = pieceLocX;
        this.pieceLocY = pieceLocY;

      }

      public int getPieceLocX() {

        return pieceLocX;

      }

      public int getPieceLocY() {

        return pieceLocY;

      }

      private int pieceLocX, pieceLocY;

    }

    class PieceLocationSequence {

      public void reset() {

        this.size = 0;

      }

      public void add(int pieceLocX, int pieceLocY) {

        if (size == capacity) {

          pieceLocations.add(new PieceLocation(pieceLocX, pieceLocY));

          ++capacity;

        }

        else

          pieceLocations.get(size).setLocation(pieceLocX, pieceLocY);

        ++size;

      }

      public PieceLocation get(int index) {

        return pieceLocations.get(index);

      }

      private int size = 0, capacity = 0;

      List<PieceLocation> pieceLocations = new ArrayList<PieceLocation>();

    }

    private PieceLocationSequence traversedPieceLocations = new PieceLocationSequence();

    private void strideY(int fromPacLocY, int toPacLocY) {

      if (fromPacLocY > toPacLocY)
        return;

      int fromPacPieceLocY = fromPacLocY / pieceHeight;

      int toPacPieceLocY = toPacLocY / pieceHeight;

      int pacPieceLocX = pacLocX / pieceWidth;

      for (int pieceLocY = fromPacPieceLocY; pieceLocY <= toPacPieceLocY; pieceLocY = (pieceLocY + 1) % mazeHeight) {

        int foodLocY = pieceLocY * pieceHeight + pieceHeight / 2;

        if (fromPacLocY <= foodLocY && foodLocY <= toPacLocY) {

          // check just in case

          if (this instanceof PacPiece && mazePieces[pieceLocY][pacPieceLocX] instanceof MazeSpace) {

            if (((MazeSpace) mazePieces[pieceLocY][pacPieceLocX]).type == MazeSpace.PILL_POWER) {

              powerPillEaten();

            } else if (((MazeSpace) mazePieces[pieceLocY][pacPieceLocX]).type == MazeSpace.PILL_REGULAR) {

              regularPillEaten();

            }

            ((MazeSpace) mazePieces[pieceLocY][pacPieceLocX]).type = MazeSpace.BLANK;

          }

          else if (this instanceof GhostPiece && ((GhostPiece) this).ghostMode instanceof GhostModeCaught
              && pacPieceLocX == homeLocX / pieceWidth && pieceLocY == homeLocY / pieceHeight) {

            System.out.println("Reached original enemy coordinates.");

            ((GhostPiece) this).setMode(GhostModeScatter.instance());

          }

        }

        // ensure we exit loop even in case we're at the boundary of the maze

        if (pieceLocY == toPacPieceLocY)

          break;

      }

    }

    void setSpeed(float speed) {

      this.speed = speed;

    }

    // sprite speed in pixels per second

    private float speed = 30.0f;

    // surplus time to be accounted for during next update

    private float accumTime = 0.0f;

    //

    private int recursionLevel = 0;

    // update world given that specified amount of seconds have passed

    public void move(float deltaTime) {

      ++recursionLevel;

      setDirection(directionEvent);

      int deltaDisp = new Float(accumTime + speed * deltaTime).intValue();

      accumTime = accumTime + speed * deltaTime - deltaDisp;

      int unusedDisp = 0;

      int maxDeltaDisp = getMaxLinearDeltaDisp();

      if (deltaDisp > maxDeltaDisp) {

        unusedDisp = deltaDisp - maxDeltaDisp;

        deltaDisp = maxDeltaDisp;

      }

      switch (pacDirection) {

        case DIR_RIGHT:

          setPacLocX((pacLocX + deltaDisp) % (World.mazeWidth * World.pieceWidth));

          break;

        case DIR_LEFT:

          setPacLocX((World.mazeWidth * World.pieceWidth + pacLocX - deltaDisp) % (World.mazeWidth * World.pieceWidth));

          break;

        case DIR_DOWN:

          setPacLocY((pacLocY + deltaDisp) % (World.mazeHeight * World.pieceHeight));

          break;

        case DIR_UP:

          setPacLocY((World.mazeHeight * World.pieceHeight + pacLocY - deltaDisp) % (World.mazeHeight * World.pieceHeight));

          break;

      }
      
      System.out.println("got here");

      if (getMaxLinearDeltaDisp() == 0 && pacNextDirection != pacDirection) {

        directionEvent = pacNextDirection;

        directionChanged();

        move(unusedDisp / speed);

      }

      if (recursionLevel == 1) {

      }

      --recursionLevel;

    }

    void directionChanged() {

    }

    public void setDirection(int direction) {

      if (isOppositeDirection(direction))

        pacNextDirection = pacDirection = direction;

      else if (getMaxLinearDeltaDisp() > 0)

        pacNextDirection = direction;

      else

        pacNextDirection = pacDirection = direction;

    }

    private boolean isOppositeDirection(int direction) {

      return pacDirection == DIR_LEFT && direction == DIR_RIGHT ||
          pacDirection == DIR_RIGHT && direction == DIR_LEFT ||
          pacDirection == DIR_DOWN && direction == DIR_UP ||
          pacDirection == DIR_UP && direction == DIR_DOWN;

    }

    public int getMaxLinearDeltaDisp() {

      int maxDeltaDispUntilWall = getMaxDeltaDispUntilWall();
      
      System.out.println("maxDeltaDispUntilWall"+ maxDeltaDispUntilWall);

      // System.out.println("maxDeltaDispUntilWall: " + maxDeltaDispUntilWall);

      int maxDeltaDispUntilNextTurn = getMaxDeltaDispUntilNextTurn();

      System.out.println("maxDeltaDispUntilNextTurn"+ maxDeltaDispUntilNextTurn);
      
      // System.out.println("maxDeltaDispUntilNextTurn: " + maxDeltaDispUntilNextTurn);

      return maxDeltaDispUntilWall < maxDeltaDispUntilNextTurn ? maxDeltaDispUntilWall : maxDeltaDispUntilNextTurn;

    }

    private int getMaxDeltaDispUntilNextTurn() {

      int maxDeltaDisp = 0;

      int pacPieceLocX = pacLocX / pieceWidth;

      int pacPieceLocY = pacLocY / pieceHeight;

      if (pacDirection == pacNextDirection)

        // no turning direction specified so return maze equivalent of infinity

        switch (pacDirection) {

          case DIR_RIGHT:
          case DIR_LEFT:

            return mazeWidth * pieceWidth;

          case DIR_DOWN:
          case DIR_UP:

            return mazeHeight * pieceHeight;

        }

      switch (pacDirection) {

        case DIR_RIGHT: {

          int centerDisp = pieceWidth / 2 - pacLocX % pieceWidth;

          int j;

          if (centerDisp < 0) {

            j = (pacPieceLocX + 1) % mazeWidth;

            maxDeltaDisp = -centerDisp + pieceWidth / 2;

          } else {

            j = pacPieceLocX;

            maxDeltaDisp = centerDisp;

          }

          for (int count = 0; count < mazeWidth; count++, j = (j + 1) % mazeWidth) {

            switch (pacNextDirection) {

              case DIR_DOWN:

                if (mazePieces[(pacPieceLocY + 1) % mazeHeight][j] instanceof MazeSpace)

                  return maxDeltaDisp;

                break;

              case DIR_UP:

                if (mazePieces[(mazeHeight + pacPieceLocY - 1) % mazeHeight][j] instanceof MazeSpace)

                  return maxDeltaDisp;

                break;

              default:

                System.out.println("Logic error.");

                break;
            }

            maxDeltaDisp += pieceWidth;

          }

          return maxDeltaDisp;

        }

        case DIR_LEFT: {

          int centerDisp = pacLocX % pieceWidth - pieceWidth / 2;

          int j;

          if (centerDisp < 0) {

            j = (mazeWidth + pacPieceLocX - 1) % mazeWidth;

            maxDeltaDisp = -centerDisp + pieceWidth / 2;

          } else {

            j = pacPieceLocX;

            maxDeltaDisp = centerDisp;

          }

          for (int count = 0; count < mazeWidth; count++, j = (mazeWidth + j - 1) % mazeWidth) {

            switch (pacNextDirection) {

              case DIR_DOWN:

                if (mazePieces[(pacPieceLocY + 1) % mazeHeight][j] instanceof MazeSpace)

                  return maxDeltaDisp;

                break;

              case DIR_UP:

                if (mazePieces[(mazeHeight + pacPieceLocY - 1) % mazeHeight][j] instanceof MazeSpace)

                  return maxDeltaDisp;

                break;

              default:

                System.out.println("Logic error.");

                break;
            }

            maxDeltaDisp += pieceWidth;

          }

          return maxDeltaDisp;

        }

        case DIR_DOWN: {

          int centerDisp = pieceHeight / 2 - pacLocY % pieceHeight;

          int i;

          if (centerDisp < 0) {

            i = (pacPieceLocY + 1) % mazeHeight;

            maxDeltaDisp = -centerDisp + pieceHeight / 2;

          } else {

            i = pacPieceLocY;

            maxDeltaDisp = centerDisp;

          }

          for (int count = 0; count < mazeHeight; count++, i = (i + 1) % mazeHeight) {

            switch (pacNextDirection) {

              case DIR_RIGHT:

                if (mazePieces[i][(pacPieceLocX + 1) % mazeWidth] instanceof MazeSpace)

                  return maxDeltaDisp;

                break;

              case DIR_LEFT:

                if (mazePieces[i][(mazeWidth + pacPieceLocX - 1) % mazeWidth] instanceof MazeSpace)

                  return maxDeltaDisp;

                break;

              default:

                System.out.println("Logic error.");

                break;
            }

            maxDeltaDisp += pieceWidth;

          }

          return maxDeltaDisp;

        }

        case DIR_UP: {

          int centerDisp = pacLocY % pieceHeight - pieceHeight / 2;

          int i;

          if (centerDisp < 0) {

            i = (mazeHeight + pacPieceLocY - 1) % mazeHeight;

            maxDeltaDisp = -centerDisp + pieceHeight / 2;

          } else {

            i = pacPieceLocY;

            maxDeltaDisp = centerDisp;

          }

          for (int count = 0; count < mazeHeight; count++, i = (mazeHeight + i - 1) % mazeHeight) {

            switch (pacNextDirection) {

              case DIR_RIGHT:

                if (mazePieces[i][(pacPieceLocX + 1) % mazeWidth] instanceof MazeSpace)

                  return maxDeltaDisp;

                break;

              case DIR_LEFT:

                if (mazePieces[i][(mazeWidth + pacPieceLocX - 1) % mazeWidth] instanceof MazeSpace)

                  return maxDeltaDisp;

                break;

              default:

                System.out.println("Logic error.");

                break;
            }

            maxDeltaDisp += pieceWidth;

          }

          return maxDeltaDisp;

        }

      }

      // should never get here

      return 0;

    }

    private int getMaxDeltaDispUntilWall() {

      // initialize maximum displacement to zero

      int maxDeltaDisp = 0;

      // retrieve coordinates in maze pieces

      int pacPieceLocX = pacLocX / pieceWidth;

      int pacPieceLocY = pacLocY / pieceHeight;

      // check what direction we're heading in

      switch (pacDirection) {

        case DIR_RIGHT:

          // compute number of pixels until piece center

          maxDeltaDisp = pieceWidth / 2 - pacLocX % pieceWidth;

          for (int count = 0, j = (pacPieceLocX + 1) % mazeWidth; count < mazeWidth && !(mazePieces[pacPieceLocY][j] instanceof MazeWall); j = (j + 1) % mazeWidth, count++) {

            maxDeltaDisp += pieceWidth;

          }

          break;

        case DIR_LEFT:

          maxDeltaDisp = pacLocX % pieceWidth - pieceWidth / 2;

          for (int count = 0, j = (mazeWidth + pacPieceLocX - 1) % mazeWidth; count < mazeWidth && !(mazePieces[pacPieceLocY][j] instanceof MazeWall); j = (mazeWidth + j - 1) % mazeWidth, count++) {

            maxDeltaDisp += pieceWidth;

          }

          break;

        case DIR_DOWN:

          maxDeltaDisp = pieceHeight / 2 - pacLocY % pieceHeight;

          for (int count = 0, i = (pacPieceLocY + 1) % mazeHeight; count < mazeHeight && !(mazePieces[i][pacPieceLocX] instanceof MazeWall); i = (i + 1) % mazeHeight, count++) {

            maxDeltaDisp += pieceHeight;

          }

          break;

        case DIR_UP:

          maxDeltaDisp = pacLocY % pieceHeight - pieceHeight / 2;

          for (int count = 0, i = (mazeHeight + pacPieceLocY - 1) % mazeHeight; count < mazeHeight && !(mazePieces[i][pacPieceLocX] instanceof MazeWall); i = (mazeHeight + i - 1) % mazeHeight, count++) {

            maxDeltaDisp += pieceHeight;

          }

          break;

      }

      return maxDeltaDisp;

    }

    protected int getReverseDirection() {

      switch (pacDirection) {

        case DIR_LEFT:

          return DIR_RIGHT;

        case DIR_RIGHT:

          return DIR_LEFT;

        case DIR_DOWN:

          return DIR_UP;

        case DIR_UP:

          return DIR_DOWN;

      }

      // should never get here

      return 0;

    }

    protected int getLeftDirection() {

      switch (pacDirection) {

        case DIR_RIGHT:

          return DIR_UP;

        case DIR_LEFT:

          return DIR_DOWN;

        case DIR_DOWN:

          return DIR_RIGHT;

        case DIR_UP:

          return DIR_LEFT;

      }

      // should never get here

      return 0;

    }

    protected int getRightDirection() {

      switch (pacDirection) {

        case DIR_RIGHT:

          return DIR_DOWN;

        case DIR_LEFT:

          return DIR_UP;

        case DIR_DOWN:

          return DIR_LEFT;

        case DIR_UP:

          return DIR_RIGHT;

      }

      // should never get here

      return 0;

    }

    int getLeftTurnX(int forkX) {

      switch (pacDirection) {

        case DIR_RIGHT:

          return forkX;

        case DIR_LEFT:

          return forkX;

        case DIR_DOWN:

          return (forkX + 1) % mazeWidth;

        case DIR_UP:

          return (mazeWidth + forkX - 1) % mazeWidth;

      }

      // should never get here

      return 0;

    }

    int getLeftTurnY(int forkY) {

      switch (pacDirection) {

        case DIR_RIGHT:

          return (mazeHeight + forkY - 1) % mazeHeight;

        case DIR_LEFT:

          return (forkY + 1) % mazeHeight;

        case DIR_DOWN:

          return forkY;

        case DIR_UP:

          return forkY;

      }

      // should never get here

      return 0;

    }

    int getRightTurnX(int forkX) {

      switch (pacDirection) {

        case DIR_RIGHT:

          return forkX;

        case DIR_LEFT:

          return forkX;

        case DIR_DOWN:

          return (mazeWidth + forkX - 1) % mazeWidth;

        case DIR_UP:

          return (forkX + 1) % mazeWidth;
      }

      // should never get here

      return 0;

    }

    int getRightTurnY(int forkY) {

      switch (pacDirection) {

        case DIR_RIGHT:

          return (forkY + 1) % mazeHeight;

        case DIR_LEFT:

          return (mazeHeight + forkY - 1) % mazeHeight;

        case DIR_DOWN:

          return forkY;

        case DIR_UP:

          return forkY;

      }

      // should never get here

      return 0;

    }

    int getForwardTurnX(int forkX) {

      switch (pacDirection) {

        case DIR_RIGHT:

          return (forkX + 1) % mazeWidth;

        case DIR_LEFT:

          return (mazeWidth + forkX - 1) % mazeWidth;

        case DIR_DOWN:

          return forkX;

        case DIR_UP:

          return forkX;

      }

      // should never get here

      return 0;

    }

    int getForwardTurnY(int forkY) {

      switch (pacDirection) {

        case DIR_RIGHT:

          return forkY;

        case DIR_LEFT:

          return forkY;

        case DIR_DOWN:

          return (forkY + 1) % mazeHeight;

        case DIR_UP:

          return (mazeHeight + forkY - 1) % mazeHeight;

      }

      // should never get here

      return 0;

    }

    // fork coordinate return values

    protected int nextForkX;
    protected int nextForkY;

    protected int getNextForkType() {

      int pacPieceLocX = pacLocX / pieceWidth;
      int pacPieceLocY = pacLocY / pieceHeight;

      boolean forwardAvailable = false;
      boolean leftAvailable = false;
      boolean rightAvailable = false;

      int pieceLocX = pacPieceLocX, pieceLocY = pacPieceLocY;

      switch (pacDirection) {

        case DIR_RIGHT: {

          for (int count = 0; count < mazeWidth; count++) {

            if (mazePieces[(mazeHeight + pieceLocY - 1) % mazeHeight][pieceLocX] instanceof MazeSpace)

              leftAvailable = true;

            if (mazePieces[(pieceLocY + 1) % mazeHeight][pieceLocX] instanceof MazeSpace)

              rightAvailable = true;

            if (mazePieces[pieceLocY][(pieceLocX + 1) % mazeWidth] instanceof MazeSpace)

              forwardAvailable = true;

            else { // we've found a wall

              forwardAvailable = false;

              break;

            }

            if (leftAvailable || rightAvailable)

              break;

            pieceLocX = (pieceLocX + 1) % mazeWidth;

          }

          break;

        }

        case DIR_LEFT: {

          for (int count = 0; count < mazeWidth; count++) {

            if (mazePieces[(pieceLocY + 1) % mazeHeight][pieceLocX] instanceof MazeSpace)

              leftAvailable = true;

            if (mazePieces[(mazeHeight + pieceLocY - 1) % mazeHeight][pieceLocX] instanceof MazeSpace)

              rightAvailable = true;

            if (mazePieces[pieceLocY][(mazeWidth + pieceLocX - 1) % mazeWidth] instanceof MazeSpace)

              forwardAvailable = true;

            else { // we've found a wall

              forwardAvailable = false;

              break;

            }

            if (leftAvailable || rightAvailable)

              break;

            pieceLocX = (mazeWidth + pieceLocX - 1) % mazeWidth;

          }

          break;

        }

        case DIR_DOWN: {

          for (int count = 0; count < mazeWidth; count++) {

            if (mazePieces[pieceLocY][(pieceLocX + 1) % mazeWidth] instanceof MazeSpace)

              leftAvailable = true;

            if (mazePieces[pieceLocY][(mazeWidth + pieceLocX - 1) % mazeWidth] instanceof MazeSpace)

              rightAvailable = true;

            if (mazePieces[(pieceLocY + 1) % mazeHeight][pieceLocX] instanceof MazeSpace)

              forwardAvailable = true;

            else { // we've found a wall

              forwardAvailable = false;

              break;

            }

            if (leftAvailable || rightAvailable)

              break;

            pieceLocX = (pieceLocX + 1) % mazeWidth;

          }

          break;

        }

        case DIR_UP: {

          for (int count = 0; count < mazeWidth; count++) {

            if (mazePieces[pieceLocY][(mazeWidth + pieceLocX - 1) % mazeWidth] instanceof MazeSpace)

              leftAvailable = true;

            if (mazePieces[pieceLocY][(pieceLocX + 1) % mazeWidth] instanceof MazeSpace)

              rightAvailable = true;

            if (mazePieces[(mazeHeight + pieceLocY - 1) % mazeHeight][pieceLocX] instanceof MazeSpace)

              forwardAvailable = true;

            else { // we've found a wall

              forwardAvailable = false;

              break;

            }

            if (leftAvailable || rightAvailable)

              break;

            pieceLocX = (pieceLocX + 1) % mazeWidth;

          }

          break;

        }

      }

      int forkType = 0;

      if (forwardAvailable)
        forkType |= FORK_FORWARD_FLAG;
      if (leftAvailable)
        forkType |= FORK_LEFT_FLAG;
      if (rightAvailable)
        forkType |= FORK_RIGHT_FLAG;

      // System.out.println("forkType: " + forkType);

      nextForkX = pieceLocX;
      nextForkY = pieceLocY;

      return forkType;

    }

  }

  public void computeDistancesTillRandomLoc(int[][] distances) {

    // find a random free tile

    int pacLocX = random.nextInt(mazeWidth);
    int pacLocY = random.nextInt(mazeHeight);
    boolean mazeSpaceNotFound = true;
    for (int countX = 0; mazeSpaceNotFound && countX < mazeWidth; countX++)
      for (int countY = 0; mazeSpaceNotFound && countY < mazeWidth; countY++)
        if (mazePieces[pacLocY][pacLocX] instanceof MazeWall) {
          pacLocX = (pacLocX + 1) % mazeWidth;
          if (pacLocX == 0)
            pacLocY = (pacLocY + 1) % mazeHeight;
        }
        else
          mazeSpaceNotFound = false;

    // compute distances till free tile so that it is the random target

    World.this.computeDistances(distances, pacLocX, pacLocY);

  }

  protected void computeDistancesTillTarget() {

    int pacPieceLocX = pacPiece.pacLocX / pieceWidth;
    int pacPieceLocY = pacPiece.pacLocY / pieceHeight;

    computeDistances(this.distances, pacPieceLocX, pacPieceLocY);

  }

  // use breadth-first search to compute target distances

  protected void computeDistances(int[][] distances, int targetLocX, int targetLocY) {

    // reset distances so as to mark each space as undiscovered

    for (int i = 0; i < mazeHeight; i++)

      for (int j = 0; j < mazeWidth; j++)

        if (mazePieces[i][j] instanceof MazeSpace)

          ((MazeSpace) mazePieces[i][j]).distance = -1;

    // expand breadth-first from target node marking all maze space nodes

    ((MazeSpace) mazePieces[targetLocY][targetLocX]).distance = 0;

    ArrayList<Integer> innerBoundaryX = new ArrayList<Integer>();
    ArrayList<Integer> innerBoundaryY = new ArrayList<Integer>();

    ArrayList<Integer> outerBoundaryX = new ArrayList<Integer>();
    ArrayList<Integer> outerBoundaryY = new ArrayList<Integer>();

    outerBoundaryX.add(targetLocX);
    outerBoundaryY.add(targetLocY);

    for (int currDist = 1; outerBoundaryX.size() > 0; currDist++) {

      innerBoundaryX = outerBoundaryX;
      innerBoundaryY = outerBoundaryY;

      outerBoundaryX = new ArrayList<Integer>();
      outerBoundaryY = new ArrayList<Integer>();

      for (int k = 0; k < innerBoundaryX.size(); k++) {

        int pieceLocX = innerBoundaryX.get(k);
        int pieceLocY = innerBoundaryY.get(k);

        Object topPiece = mazePieces[(mazeHeight + pieceLocY - 1) % mazeHeight][pieceLocX];
        Object leftPiece = mazePieces[pieceLocY][(mazeWidth + pieceLocX - 1) % mazeWidth];
        Object rightPiece = mazePieces[pieceLocY][(pieceLocX + 1) % mazeWidth];
        Object bottomPiece = mazePieces[(pieceLocY + 1) % mazeHeight][pieceLocX];

        if (topPiece instanceof MazeSpace && ((MazeSpace) topPiece).distance == -1) {

          ((MazeSpace) topPiece).distance = currDist;
          outerBoundaryY.add((mazeHeight + pieceLocY - 1) % mazeHeight);
          outerBoundaryX.add(pieceLocX);
          // ((MazeSpace) topPiece).type = MazeSpace.PILL_POWER;

        }

        if (leftPiece instanceof MazeSpace && ((MazeSpace) leftPiece).distance == -1) {

          ((MazeSpace) leftPiece).distance = currDist;
          outerBoundaryY.add(pieceLocY);
          outerBoundaryX.add((mazeWidth + pieceLocX - 1) % mazeWidth);
          // ((MazeSpace) leftPiece).type = MazeSpace.PILL_POWER;

        }

        if (rightPiece instanceof MazeSpace && ((MazeSpace) rightPiece).distance == -1) {

          ((MazeSpace) rightPiece).distance = currDist;
          outerBoundaryY.add(pieceLocY);
          outerBoundaryX.add((pieceLocX + 1) % mazeWidth);
          // ((MazeSpace) rightPiece).type = MazeSpace.PILL_POWER;

        }
        if (bottomPiece instanceof MazeSpace && ((MazeSpace) bottomPiece).distance == -1) {

          ((MazeSpace) bottomPiece).distance = currDist;
          outerBoundaryY.add((pieceLocY + 1) % mazeHeight);
          outerBoundaryX.add(pieceLocX);
          // ((MazeSpace) bottomPiece).type = MazeSpace.PILL_POWER;

        }

      }

    }

    for (int i = 0; i < mazeHeight; i++)

      for (int j = 0; j < mazeWidth; j++)

        if (mazePieces[i][j] instanceof MazeSpace)

          distances[i][j] = ((MazeSpace) mazePieces[i][j]).distance;

  }

  void presUpdatePowerBlinkState() {

    presPowerBlinkState = ++presPowerBlinkState % numPresPowerBlinkStates;

  }

  void presResetPowerBlinkState() {

    presPowerBlinkState = 0;

  }

  void presUpdateGhostBlinkState() {

    presGhostBlinkState = ++presGhostBlinkState % numPresGhostBlinkStates;

  }

  private static final int FORK_FORWARD_FLAG = 0x1;
  private static final int FORK_LEFT_FLAG = 0x2;
  private static final int FORK_RIGHT_FLAG = 0x4;

  private static final int FORK_BLIND = 0; // path blocked left, right, and forward
  private static final int FORK_NONE = 0x1; // path continues only forward forever
  private static final int FORK_LEFT = 0x2;
  private static final int FORK_RIGHT = 0x4;
  private static final int FORK_LEFT_RIGHT = FORK_LEFT_FLAG | FORK_RIGHT_FLAG;
  private static final int FORK_LEFT_FORWARD = FORK_LEFT_FLAG | FORK_FORWARD_FLAG;
  private static final int FORK_RIGHT_FORWARD = FORK_RIGHT_FLAG | FORK_FORWARD_FLAG;
  private static final int FORK_LEFT_RIGHT_FORWARD = FORK_LEFT_FLAG | FORK_RIGHT_FLAG | FORK_FORWARD_FLAG;

  public static final int mazeWidth = 21;
  public static final int mazeHeight = 16;

  static final char symbolWall = '#';
  static final char symbolPill = '.';
  static final char symbolPower = 'o';
  static final char symbolBlank = ' ';
  static final char symbolOrigin = 'C';
  static final char symbolEnemyBlinky = 'W';
  static final char symbolEnemyPinky = 'X';
  static final char symbolEnemyInky = 'Y';
  static final char symbolEnemyClyde = 'Z';

  public Object[][] mazePieces = new Object[mazeHeight][mazeWidth];

  void readEmptyMaze() {

    for (int i = 0; i < mazeHeight; i++)

      for (int j = 0; j < mazeWidth; j++) {

        mazeMap[i][j] = symbolBlank;

        mazePieces[i][j] = new MazeSpace(MazeSpace.BLANK);

      }

  }

  void setNumWorlds() {

    numWorlds = 0;

    try {

      for (;;) {

        String fileName = "maze" + (numWorlds + 1) + ".txt";

        new BufferedReader(new InputStreamReader(new FileInputStream(new File(context.getFilesDir() + "/" + fileName))));

        ++numWorlds;

      }

    } catch (IOException e) {
    }

  }

  void addWorld() {

    worldNum = numWorlds + 1;

    writeMaze();

    ++numWorlds;

  }

  void modifyWorld() {

    writeMaze();

  }

  void deleteWorld() {

    if (worldNum > 0) {

      for (int k = worldNum + 1; k <= numWorlds; k++) {

        String inputFileName = "maze" + k + ".txt";

        String outputFileName = "maze" + (k - 1) + ".txt";

        try {

          FileChannel in = new FileInputStream(context.getFilesDir() + "/" + inputFileName).getChannel();
          FileChannel out = new FileOutputStream(context.getFilesDir() + "/" + outputFileName).getChannel();
          in.transferTo(0, (int) in.size(), out);
          in.close();
          out.close();

        } catch (IOException e) {
          e.printStackTrace();
        }

      }

    }

    String fileName = "maze" + numWorlds + ".txt";

    new File(context.getFilesDir() + "/" + fileName).delete();

    if (worldNum == numWorlds--)

      --worldNum;

    readMaze();

    setMaze();

  }

  void writeMaze() {

    String outputFileName = "maze" + worldNum + ".txt";

    try {

      FileOutputStream fos = new FileOutputStream(context.getFilesDir() + "/" + outputFileName);

      BufferedWriter writer = new BufferedWriter(new OutputStreamWriter(fos));

      for (int i = 0; i < mazeHeight; i++) {

        for (int j = 0; j < mazeWidth; j++)

          writer.write(mazeMap[i][j]);

        writer.write("\n");

      }

      writer.close();

    } catch (IOException e) {

      e.printStackTrace();

    }

  }

  void initMazes() {

    try {

      File checkFile = new File(context.getFilesDir() + "mazes.txt");

      if (!checkFile.exists()) {

        checkFile.createNewFile();

        for (int k = 1; k <= 2; k++) {

          String inputFileName = "mazes/maze" + k + ".txt";
          String outputFileName = "maze" + k + ".txt";

          InputStream in = context.getAssets().open(inputFileName);
          OutputStream out = new FileOutputStream(context.getFilesDir() + "/" + outputFileName);

          byte[] buf = new byte[1024];

          int len;

          while ((len = in.read(buf)) > 0)

            out.write(buf, 0, len);

          in.close();

          out.close();

        }

      }

    } catch (IOException e) {
      e.printStackTrace();
    }

  }

  void readMaze() {

    if (worldNum == 0) // untested

      readEmptyMaze();

    else {

      String fileName = "maze" + worldNum + ".txt";

      System.out.println(fileName);

      try {

        BufferedReader reader = new BufferedReader(new InputStreamReader(new FileInputStream(new File(context.getFilesDir() + "/" + fileName))));

        for (int i = 0; i < mazeHeight; i++) {

          for (int j = 0; j < mazeWidth; j++)

            mazeMap[i][j] = (char) reader.read();

          reader.read();

        }

        reader.close();

      } catch (IOException e) {

        e.printStackTrace();

      }

    }

    setMaze();

  }

  void setMaze() {

    pacPiece = null;

    ghostPieces = new ArrayList<GhostPiece>();

    for (int i = 0; i < mazeHeight; i++) {

      for (int j = 0; j < mazeWidth; j++)

        switch (mazeMap[i][j]) {

          case symbolWall:

            mazePieces[i][j] = new MazeWall(topIsBlocked(i, j), leftIsBlocked(i, j), rightIsBlocked(i, j), bottomIsBlocked(i, j));

            break;

          case symbolPill:

            mazePieces[i][j] = new MazeSpace(MazeSpace.PILL_REGULAR);

            break;

          case symbolPower:

            mazePieces[i][j] = new MazeSpace(MazeSpace.PILL_POWER);

            break;

          case symbolBlank:

            mazePieces[i][j] = new MazeSpace(MazeSpace.BLANK);

            break;

          case symbolOrigin:

            mazePieces[i][j] = new MazeSpace(MazeSpace.BLANK);

            pacPiece = new PacPiece(pieceWidth * j + pieceWidth / 2, pieceHeight * i + pieceHeight / 2);

            System.out.println("Created pac piece.");

            break;

          case symbolEnemyBlinky:

            mazePieces[i][j] = new MazeSpace(MazeSpace.BLANK);

            ghostPieces.add(new GhostPiece(this, GhostPiece.TYPE_BLINKY, pieceWidth * j + pieceWidth / 2, pieceHeight * i + pieceHeight / 2));

            System.out.println("Created ghost piece.");

            break;

          case symbolEnemyPinky:

            mazePieces[i][j] = new MazeSpace(MazeSpace.BLANK);

            ghostPieces.add(new GhostPiece(this, GhostPiece.TYPE_PINKY, pieceWidth * j + pieceWidth / 2, pieceHeight * i + pieceHeight / 2));

            System.out.println("Created ghost piece.");

            break;

          case symbolEnemyInky:

            mazePieces[i][j] = new MazeSpace(MazeSpace.BLANK);

            ghostPieces.add(new GhostPiece(this, GhostPiece.TYPE_INKY, pieceWidth * j + pieceWidth / 2, pieceHeight * i + pieceHeight / 2));

            System.out.println("Created ghost piece.");

            break;

          case symbolEnemyClyde:

            mazePieces[i][j] = new MazeSpace(MazeSpace.BLANK);

            ghostPieces.add(new GhostPiece(this, GhostPiece.TYPE_CLYDE, pieceWidth * j + pieceWidth / 2, pieceHeight * i + pieceHeight / 2));

            System.out.println("Created ghost piece.");

            break;

        }

    }

    for (int i = 0; i < mazeHeight; i++) {

      for (int j = 0; j < mazeWidth; j++)

        System.out.print(mazeMap[i][j]);

      System.out.println("");

    }

    // initialize data structures dependent on existence of maze pieces

    init();

  }

  char[][] mazeMap = new char[mazeHeight][mazeWidth];

  private final boolean topIsBlocked(int i, int j) {
    if (i == 0)
      return false;
    else
      return isWall(i - 1, j);
  }

  private final boolean leftIsBlocked(int i, int j) {
    if (j == 0)
      return false;
    else
      return isWall(i, j - 1);
  }

  private final boolean rightIsBlocked(int i, int j) {
    if (j == mazeWidth - 1)
      return false;
    else
      return isWall(i, j + 1);
  }

  private final boolean bottomIsBlocked(int i, int j) {
    if (i == mazeHeight - 1)
      return false;
    else
      return isWall(i + 1, j);
  }

  private final boolean isWall(int i, int j) {
    return mazeMap[i][j] == symbolWall ? true : false;
  }

  class MazeWall {

    public MazeWall(boolean topIsBlocked, boolean leftIsBlocked, boolean rightIsBlocked, boolean bottomIsBlocked) {

      if (topIsBlocked)
        adjacentWallBits |= 0x8;
      if (leftIsBlocked)
        adjacentWallBits |= 0x4;
      if (rightIsBlocked)
        adjacentWallBits |= 0x2;
      if (bottomIsBlocked)
        adjacentWallBits |= 0x1;

    }

    public int adjacentWallBits = 0;

  }

  class MazeSpace {

    public MazeSpace(int type) {

      this.type = type;

    }

    public int type;

    public int distance; // distance to target used as temporary place holder

    public static final int PILL_REGULAR = 2;
    public static final int PILL_POWER = 1;
    public static final int BLANK = 0;

  }

  class IntegerListFuncs {

    IntegerListFuncs(List<Integer> values) {

      this.values = values;

    }

    private List<Integer> values;

    int getRandMinIndex() {

      List<IndexedValue> indexedValues = new ArrayList<IndexedValue>();

      for (int i = 0; i < this.values.size(); i++) {

        indexedValues.add(new IndexedValue(values.get(i), i));

      }

      Collections.<IndexedValue> sort(indexedValues);

      int equalLength = 1;

      for (int i = 1; i < indexedValues.size(); i++) {

        if (indexedValues.get(i).value == indexedValues.get(0).value)

          ++equalLength;

      }

      int index = random.nextInt(equalLength);

      return indexedValues.get(index).index;

    }

    class IndexedValue implements Comparable<IndexedValue> {

      IndexedValue(int index, int value) {

        this.index = index;

        this.value = value;

      }

      public int compareTo(IndexedValue indexedValue) {

        return new Integer(this.value).compareTo(new Integer(indexedValue.value));

      }

      public int index;

      public int value;

    }

  }

}
